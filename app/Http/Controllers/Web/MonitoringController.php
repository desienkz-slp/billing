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

class MonitoringController extends Controller
{
    protected MikroTikService $mikrotik;

    public function __construct(MikroTikService $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }

    /**
     * Dashboard — list all routers with status.
     */
    public function index()
    {
        $routers = Router::where('is_active', true)
            ->withCount('customers')
            ->orderBy('name')
            ->get();

        return Inertia::render('Monitoring/Index', compact('routers'));
    }

    /**
     * Router detail page with PPPoE data.
     */
    public function routerDetail(Router $router)
    {
        // Get PPPoE secrets from router live
        $secrets = $this->mikrotik->getPppoeSecrets($router);

        // Get active connections
        $active = $this->mikrotik->getActiveConnections($router);
        $activeNames = collect($active)->pluck('name')->toArray();

        // Get system resources
        $resources = $this->mikrotik->getSystemResources($router);
        $identity = $resources['board-name'] ?? ($this->mikrotik->getIdentity($router) ?? $router->name);

        // Match secrets with local customers
        $customerMap = Customer::where('router_id', $router->id)
            ->whereNotNull('username')
            ->pluck('name', 'username')
            ->toArray();

        $customerIdMap = Customer::where('router_id', $router->id)
            ->whereNotNull('username')
            ->pluck('id', 'username')
            ->toArray();

        // Build enriched secrets list
        $enrichedSecrets = collect($secrets)->map(function ($s) use ($activeNames, $customerMap, $customerIdMap) {
            $name = $s['name'] ?? '';
            return [
                'name' => $name,
                'password' => $s['password'] ?? '***',
                'profile' => $s['profile'] ?? '-',
                'service' => $s['service'] ?? 'pppoe',
                'disabled' => ($s['disabled'] ?? 'false') === 'true',
                'comment' => $s['comment'] ?? '',
                'is_online' => in_array($name, $activeNames),
                'customer_name' => $customerMap[$name] ?? null,
                'customer_id' => $customerIdMap[$name] ?? null,
            ];
        })->sortByDesc('is_online')->values();

        // Active connection details
        $activeDetails = collect($active)->map(function ($a) use ($customerMap) {
            return [
                'name' => $a['name'] ?? '',
                'service' => $a['service'] ?? '',
                'caller_id' => $a['caller-id'] ?? '',
                'address' => $a['address'] ?? '',
                'uptime' => $a['uptime'] ?? '',
                'encoding' => $a['encoding'] ?? '',
                'customer_name' => $customerMap[$a['name'] ?? ''] ?? null,
            ];
        });

        // Get profiles
        $profiles = $this->mikrotik->getPppoeProfiles($router);

        return Inertia::render('Monitoring/RouterDetail', compact(
            'router', 'enrichedSecrets', 'activeDetails', 'profiles', 'resources', 'identity'
        ));
    }

    /**
     * JSON: Active connections for a router.
     */
    public function activeConnections(Router $router)
    {
        $active = $this->mikrotik->getActiveConnections($router);
        return response()->json(['status' => 'success', 'data' => $active, 'count' => count($active)]);
    }

    /**
     * JSON: System resources for a router.
     */
    public function systemResources(Router $router)
    {
        $resources = $this->mikrotik->getSystemResources($router);
        if (!$resources) {
            return response()->json(['status' => 'error', 'message' => 'Tidak bisa konek ke router.']);
        }
        return response()->json(['status' => 'success', 'data' => $resources]);
    }

    /**
     * Sync PPPoE secrets & profiles from router to database.
     */
    public function syncPppoe(Router $router)
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
     * Isolir customer via MikroTik.
     */
    public function isolirCustomer(Router $router, Customer $customer)
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
                'details' => "Isolir via monitoring - Router: {$router->name}",
            ]);

            return response()->json(['status' => 'success', 'message' => "{$customer->name} berhasil di-isolir."]);
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal isolir. Cek koneksi ke router.']);
    }

    /**
     * Unisolir customer via MikroTik.
     */
    public function unisolirCustomer(Router $router, Customer $customer)
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
                'details' => "Unisolir via monitoring - Router: {$router->name}",
            ]);

            return response()->json(['status' => 'success', 'message' => "{$customer->name} berhasil di-unisolir."]);
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal unisolir. Cek koneksi ke router.']);
    }
}
