<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\IsolirLog;
use App\Models\PppoeProfile;
use App\Models\PppoeSecret;
use App\Models\Router;
use App\Services\MikroTik\MikroTikService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RadiusController extends Controller
{
    protected MikroTikService $mikrotik;

    public function __construct(MikroTikService $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }

    /**
     * RADIUS Dashboard — list routers for PPPoE management.
     */
    public function index()
    {
        $routers = Router::where('is_active', true)
            ->withCount('customers')
            ->withCount('pppoeSecrets')
            ->withCount('pppoeProfiles')
            ->orderBy('name')
            ->get();

        return Inertia::render('Radius/Index', compact('routers'));
    }

    /**
     * PPPoE Secrets for a router.
     */
    public function secrets(Router $router)
    {
        // Live fetch from router
        $secrets = $this->mikrotik->getPppoeSecrets($router);
        $active = $this->mikrotik->getActiveConnections($router);
        $activeNames = collect($active)->pluck('name')->toArray();

        // Match with local customers
        $customerMap = Customer::where('router_id', $router->id)
            ->whereNotNull('username')
            ->get(['id', 'name', 'username', 'status', 'is_isolated'])
            ->keyBy('username');

        $enrichedSecrets = collect($secrets)->map(function ($s) use ($activeNames, $customerMap) {
            $name = $s['name'] ?? '';
            $customer = $customerMap[$name] ?? null;
            return [
                'name' => $name,
                'password' => $s['password'] ?? '***',
                'profile' => $s['profile'] ?? '-',
                'service' => $s['service'] ?? 'pppoe',
                'disabled' => ($s['disabled'] ?? 'false') === 'true',
                'comment' => $s['comment'] ?? '',
                'is_online' => in_array($name, $activeNames),
                'customer_name' => $customer?->name,
                'customer_id' => $customer?->id,
                'customer_status' => $customer?->status,
                'customer_isolated' => $customer?->is_isolated,
            ];
        })->sortByDesc('is_online')->values();

        $profiles = $this->mikrotik->getPppoeProfiles($router);

        return Inertia::render('Radius/Secrets', compact('router', 'enrichedSecrets', 'profiles', 'active'));
    }

    /**
     * PPPoE Profiles for a router.
     */
    public function profiles(Router $router)
    {
        $profiles = $this->mikrotik->getPppoeProfiles($router);
        return response()->json(['status' => 'success', 'data' => $profiles]);
    }

    /**
     * Sync PPPoE data from router to local DB.
     */
    public function sync(Router $router)
    {
        $tenantId = session('tenant_id');

        // Sync profiles
        $profiles = $this->mikrotik->getPppoeProfiles($router);
        $syncedProfiles = 0;
        foreach ($profiles as $p) {
            if (!isset($p['name'])) continue;
            PppoeProfile::updateOrCreate(
                ['tenant_id' => $tenantId, 'router_id' => $router->id, 'name' => $p['name']],
                [
                    'rate_limit' => $p['rate-limit'] ?? null,
                    'local_address' => $p['local-address'] ?? null,
                    'remote_address' => $p['remote-address'] ?? null,
                    'mikrotik_data' => $p,
                ]
            );
            $syncedProfiles++;
        }

        // Sync secrets
        $secrets = $this->mikrotik->getPppoeSecrets($router);
        $customerUsernames = Customer::whereNotNull('username')
            ->pluck('id', 'username')
            ->toArray();

        $syncedSecrets = 0;
        foreach ($secrets as $s) {
            if (!isset($s['name'])) continue;
            PppoeSecret::updateOrCreate(
                ['tenant_id' => $tenantId, 'router_id' => $router->id, 'name' => $s['name']],
                [
                    'customer_id' => $customerUsernames[$s['name']] ?? null,
                    'password_plain' => $s['password'] ?? null,
                    'profile' => $s['profile'] ?? null,
                    'service' => $s['service'] ?? 'pppoe',
                    'disabled' => ($s['disabled'] ?? 'false') === 'true',
                    'comment' => $s['comment'] ?? null,
                    'mikrotik_data' => $s,
                ]
            );
            $syncedSecrets++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Sync selesai: {$syncedProfiles} profile, {$syncedSecrets} secret.",
        ]);
    }

    /**
     * Isolir customer via router.
     */
    public function isolir(Router $router, Customer $customer)
    {
        if (!$customer->username) {
            return response()->json(['status' => 'error', 'message' => 'Customer tidak punya username PPPoE.']);
        }

        $success = $this->mikrotik->disableSecret($router, $customer->username);
        if ($success) {
            $customer->update(['is_isolated' => true, 'isolated_since' => now()]);
            IsolirLog::create([
                'tenant_id' => session('tenant_id'),
                'customer_id' => $customer->id,
                'action' => 'isolir',
                'method' => 'manual',
                'executed_by' => auth()->id(),
                'success' => true,
                'details' => "Isolir via RADIUS - Router: {$router->name}",
            ]);
            return response()->json(['status' => 'success', 'message' => "{$customer->name} berhasil di-isolir."]);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal isolir. Cek koneksi ke router.']);
    }

    /**
     * Unisolir customer via router.
     */
    public function unisolir(Router $router, Customer $customer)
    {
        if (!$customer->username) {
            return response()->json(['status' => 'error', 'message' => 'Customer tidak punya username PPPoE.']);
        }

        $success = $this->mikrotik->enableSecret($router, $customer->username);
        if ($success) {
            $customer->update(['is_isolated' => false, 'isolated_since' => null]);
            IsolirLog::create([
                'tenant_id' => session('tenant_id'),
                'customer_id' => $customer->id,
                'action' => 'unisolir',
                'method' => 'manual',
                'executed_by' => auth()->id(),
                'success' => true,
                'details' => "Unisolir via RADIUS - Router: {$router->name}",
            ]);
            return response()->json(['status' => 'success', 'message' => "{$customer->name} berhasil di-unisolir."]);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal unisolir. Cek koneksi ke router.']);
    }
}
