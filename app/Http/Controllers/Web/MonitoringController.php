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
            ->withCount(['customers', 'pppoeSecrets', 'pppoeProfiles'])
            ->orderBy('id')
            ->get();

        $allUsers = collect();

        // Enrich each router with live data
        $enrichedRouters = $routers->map(function ($router) use (&$allUsers) {
            // Get active connections
            $onlineCount = 0;
            $liveStatus = 'pending';
            $activeConnections = [];
            $pppoeInterfaces = [];
            try {
                $activeConnections = $this->mikrotik->getActiveConnections($router);
                $pppoeInterfaces = $this->mikrotik->getPppoeServerInterfaces($router);
                $onlineCount = count($activeConnections);
                $liveStatus = 'ok';
            } catch (\Throwable $e) {
                $liveStatus = 'error';
            }
            
            $activeNames = collect($activeConnections)->pluck('name')->toArray();
            $activeMap = collect($activeConnections)->keyBy('name');
            $interfaceMap = collect($pppoeInterfaces)->keyBy('user');

            // Get customer map
            $customerMap = Customer::where('router_id', $router->id)
                ->whereNotNull('username')
                ->pluck('name', 'username')
                ->toArray();

            // Get PPPoE secrets from MikroTik
            $secrets = [];
            $profiles = [];
            try {
                $secrets = $this->mikrotik->getPppoeSecrets($router);
                $profiles = $this->mikrotik->getPppoeProfiles($router);
            } catch (\Throwable $e) {}

            // Merge names from secrets and active connections
            $allNames = array_unique(array_merge(
                collect($secrets)->pluck('name')->toArray(),
                $activeNames
            ));
            
            $secretsMap = collect($secrets)->keyBy('name');

            // Build user rows for PPPoE table
            foreach ($allNames as $name) {
                if (empty($name)) continue;

                $s = $secretsMap->get($name, []);
                $isOnline = in_array($name, $activeNames);
                $activeInfo = $activeMap->get($name, []);
                $ifaceInfo = $interfaceMap->get($name, []);

                $finalProfile = $s['profile'] ?? 'Radius/Dynamic';
                
                // Construct interface display
                $interfaceDisp = '-';
                if ($isOnline) {
                    $mac = $activeInfo['caller-id'] ?? '';
                    $iface = $ifaceInfo['interface'] ?? '';
                    
                    if ($iface) {
                        $interfaceDisp = $iface;
                    } elseif ($mac) {
                        $interfaceDisp = $mac;
                    }
                }

                $allUsers->push([
                    'name' => $name,
                    'router_id' => $router->id,
                    'router_name' => $router->name,
                    'profile' => $finalProfile,
                    'service' => $activeInfo['service'] ?? ($s['service'] ?? 'pppoe'),
                    'disabled' => ($s['disabled'] ?? 'false') === 'true',
                    'is_online' => $isOnline,
                    'ip' => $isOnline ? ($activeInfo['address'] ?? '-') : ($s['remote-address'] ?? '-'),
                    'caller_id' => $interfaceDisp,
                    'uptime' => $isOnline ? ($activeInfo['uptime'] ?? '-') : '-',
                    'customer_name' => $customerMap[$name] ?? null,
                    'last_logout' => $s['last-logged-out'] ?? '-',
                ]);
            }

            // Get mapped (linked) customers from secrets (local DB for now)
            $mappedCustomers = $router->pppoeSecrets()
                ->whereNotNull('customer_id')
                ->count();

            // Get disabled secrets count
            $disabledSecrets = collect($secrets)->where('disabled', 'true')->count();

            // Get last sync time
            $lastSecretSync = $router->pppoeSecrets()->max('updated_at');
            $lastProfileSync = $router->pppoeProfiles()->max('updated_at');
            $lastSync = max($lastSecretSync, $lastProfileSync);

            return [
                'id' => $router->id,
                'name' => $router->name,
                'ip_address' => $router->ip_address,
                'port' => $router->port,
                'description' => $router->description,
                'is_active' => $router->is_active,
                'customers_count' => $router->customers_count, // Still local DB
                'pppoe_secrets_count' => count($secrets), // Use live count
                'pppoe_profiles_count' => count($profiles), // Use live count
                'disabled_secrets_count' => $disabledSecrets,
                'mapped_customers' => $mappedCustomers,
                'online_count' => $onlineCount,
                'live_status' => $liveStatus,
                'last_sync' => $lastSync,
            ];
        });

        // Sort users: online first, then alphabetical
        $sortedUsers = $allUsers->sortBy([
            ['is_online', 'desc'],
            ['name', 'asc'],
        ])->values();

        // Summary stats
        $summary = [
            'total_routers' => $enrichedRouters->count(),
            'total_online' => $enrichedRouters->sum('online_count'),
            'total_secrets' => $enrichedRouters->sum('pppoe_secrets_count'),
            'total_profiles' => $enrichedRouters->sum('pppoe_profiles_count'),
            'total_customers' => $enrichedRouters->sum('customers_count'),
            'mapped_customers' => $enrichedRouters->sum('mapped_customers'),
            'healthy_routers' => $enrichedRouters->where('live_status', 'ok')->count(),
            'error_routers' => $enrichedRouters->where('live_status', 'error')->count(),
        ];

        return Inertia::render('Monitoring/Index', [
            'routers' => $enrichedRouters,
            'summary' => $summary,
            'pppoeUsers' => $sortedUsers,
        ]);
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

    /**
     * Fetch secrets for a specific router (JSON API).
     */
    public function secretsJson(Router $router)
    {
        try {
            $API = $this->mikrotik->connect($router);
            if (!$API) {
                return response()->json(['status' => 'error', 'message' => 'Gagal konek ke router. Pastikan router aktif.']);
            }

            $secrets = $API->comm('/ppp/secret/print');
            if (!is_array($secrets) || isset($secrets['!trap'])) $secrets = [];

            $active = $API->comm('/ppp/active/print');
            if (!is_array($active) || isset($active['!trap'])) $active = [];

            $profiles = $API->comm('/ppp/profile/print');
            if (!is_array($profiles) || isset($profiles['!trap'])) $profiles = [];

            $API->disconnect();

            // filter arrays
            $secrets = array_filter($secrets, 'is_array');
            $active = array_filter($active, 'is_array');
            $profiles = array_filter($profiles, 'is_array');

            $activeNames = collect($active)->pluck('name')->toArray();
            $activeUptimes = collect($active)->pluck('uptime', 'name')->toArray();

            $customerMap = Customer::where('router_id', $router->id)
                ->whereNotNull('username')
                ->get(['id', 'name', 'username', 'status', 'is_isolated'])
                ->keyBy('username');

            $enrichedSecrets = collect($secrets)->map(function ($s) use ($activeNames, $activeUptimes, $customerMap) {
                // Sanitize all string fields from MikroTik
                foreach ($s as $key => $value) {
                    if (is_string($value)) {
                        $s[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }

                $name = $s['name'] ?? '';
                $comment = $s['comment'] ?? '';
                $customer = $customerMap[$name] ?? null;
                return [
                    'name' => $name,
                    'password' => $s['password'] ?? '***',
                    'profile' => $s['profile'] ?? '-',
                    'service' => $s['service'] ?? 'pppoe',
                    'disabled' => ($s['disabled'] ?? 'false') === 'true',
                    'comment' => $comment,
                    'is_online' => in_array($name, $activeNames),
                    'customer_name' => $customer?->name,
                    'customer_id' => $customer?->id,
                    'customer_status' => $customer?->status,
                    'customer_isolated' => $customer?->is_isolated,
                    'uptime' => $activeUptimes[$name] ?? '-',
                    'last_logout' => $s['last-logged-out'] ?? '-'
                ];
            })->sortByDesc('is_online')->values();

            $sanitizedProfiles = collect($profiles)->map(function ($p) {
                foreach ($p as $key => $value) {
                    if (is_string($value)) {
                        $p[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }
                return $p;
            })->toArray();

            return response()->json([
                'status' => 'success',
                'secrets' => $enrichedSecrets,
                'profiles' => array_values($sanitizedProfiles),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Add a new PPPoE Secret to the router.
     */
    public function addSecret(Request $request, Router $router)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'profile' => 'required|string'
        ]);

        try {
            $API = $this->mikrotik->connect($router);
            if (!$API) {
                return response()->json(['status' => 'error', 'message' => 'Gagal konek ke router.']);
            }

            $API->comm('/ppp/secret/add', [
                '=name' => $validated['username'],
                '=password' => $validated['password'],
                '=profile' => $validated['profile'],
                '=service' => 'pppoe',
            ]);

            $API->disconnect();
            return response()->json(['status' => 'success', 'message' => "User {$validated['username']} berhasil ditambahkan."]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menambah user: ' . $e->getMessage()]);
        }
    }

    /**
     * Update profile for multiple PPPoE secrets.
     */
    public function updateProfile(Request $request, Router $router)
    {
        $validated = $request->validate([
            'users' => 'required|array',
            'profile' => 'required|string'
        ]);

        try {
            $API = $this->mikrotik->connect($router);
            if (!$API) {
                return response()->json(['status' => 'error', 'message' => 'Gagal konek ke router.']);
            }

            $successCount = 0;
            foreach ($validated['users'] as $username) {
                $res = $API->comm('/ppp/secret/print', ['?name' => $username]);
                if (!empty($res) && isset($res[0]['.id'])) {
                    $API->comm('/ppp/secret/set', [
                        '=.id' => $res[0]['.id'],
                        '=profile' => $validated['profile']
                    ]);
                    $successCount++;
                }
            }

            $API->disconnect();

            return response()->json(['status' => 'success', 'message' => "{$successCount} user berhasil diupdate ke profile {$validated['profile']}."]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal update profile: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a PPPoE secret.
     */
    public function deleteSecret(Request $request, Router $router)
    {
        $validated = $request->validate([
            'username' => 'required|string'
        ]);

        try {
            $API = $this->mikrotik->connect($router);
            if (!$API) {
                return response()->json(['status' => 'error', 'message' => 'Gagal konek ke router.']);
            }

            $res = $API->comm('/ppp/secret/print', ['?name' => $validated['username']]);
            if (!empty($res) && isset($res[0]['.id'])) {
                $API->comm('/ppp/secret/remove', [
                    '.id' => $res[0]['.id']
                ]);
                $API->disconnect();
                return response()->json(['status' => 'success', 'message' => "User {$validated['username']} berhasil dihapus."]);
            }

            $API->disconnect();
            return response()->json(['status' => 'error', 'message' => "User {$validated['username']} tidak ditemukan di router."]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus user: ' . $e->getMessage()]);
        }
    }
}
