<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Router;
use App\Models\RouterConfigBackup;
use App\Models\Server;
use App\Models\User;
use App\Services\MikroTik\RouterConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ConfigController extends Controller
{
    /** All can_* permission keys in our roles table */
    private array $permKeys = [
        // Dashboard
        'can_view_dashboard', 'can_view_dashboard_config', 'can_view_dashboard_map', 'can_view_dashboard_olt',
        // Customer
        'can_input_customer', 'can_edit_customer', 'can_delete_customer',
        'can_import_export_customer', 'can_view_all_customers', 'can_delete_customer_cuti',
        // Visibility
        'view_by_area', 'view_by_sales', 'view_own_only',
        // Payment
        'can_process_payment', 'can_send_wa_invoice', 'can_cancel_payment', 'can_view_payment_history',
        // Isolir & Cuti
        'can_manage_isolir', 'can_cuti', 'can_manage_cuti',
        // Finance
        'can_view_reports', 'can_view_finance', 'can_manage_expenses',
        'can_manage_deposits', 'can_manage_saldo', 'can_delete_finance',
        'can_use_deposit',
        // Master Data
        'can_manage_packages', 'can_manage_areas', 'can_manage_odp',
        'can_manage_servers',
        // Admin
        'can_manage_users', 'can_manage_roles', 'can_view_audit_logs',
        'can_backup_restore', 'can_edit_template',
        // Teknikal
        'can_view_radius', 'can_manage_radius', 'can_manage_router', 'can_manage_olt', 'can_manage_acs',
        // Komunikasi & Map
        'can_send_wa_blast', 'can_config_map', 'can_view_monitor',
        // Module Access
        'can_view_dashboard', 'can_view_dashboard_config', 'can_view_dashboard_map', 'can_view_dashboard_olt', 'can_view_acs',
        'can_access_billing', 'can_access_config', 'can_access_db', 'can_access_map',
        'can_access_desktop', 'can_access_mobile',
        // Settings
        'fee_locked', 'is_saldo_limited'
    ];

    /** Redirect to first config page */
    public function index()
    {
        return redirect()->route('config.profil');
    }

    // ==================== USERS ====================

    public function users()
    {
        $users = User::with('role')
            ->where(function ($q) {
                $q->where('is_system_admin', false)->orWhereNull('is_system_admin');
            })
            ->orderBy('name')
            ->get();
        $roles = Role::orderBy('name')->get();
        return Inertia::render('Settings/Users', compact('users', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'is_default_sales' => 'boolean',
        ]);

        $data['uuid'] = (string) Str::uuid();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;
        $data['tenant_id'] = session('tenant_id');
        $data['is_default_sales'] = $request->boolean('is_default_sales');

        if ($data['is_default_sales']) {
            User::where('tenant_id', $data['tenant_id'])->update(['is_default_sales' => false]);
        }

        User::create($data);
        return back()->with('success', "User {$data['name']} berhasil ditambahkan.");
    }

    public function updateUser(Request $request, User $targetUser)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'is_default_sales' => 'boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_default_sales'] = $request->boolean('is_default_sales');

        if ($data['is_default_sales'] && !$targetUser->is_default_sales) {
            User::where('tenant_id', $targetUser->tenant_id)->update(['is_default_sales' => false]);
        } elseif (!$data['is_default_sales'] && $targetUser->is_default_sales) {
            // Optional: prevent unsetting if they are the only default? We allow it for now.
        }

        $targetUser->update($data);
        return back()->with('success', "User {$targetUser->name} berhasil diperbarui.");
    }

    public function setDefaultSales(User $targetUser)
    {
        User::where('tenant_id', $targetUser->tenant_id)->update(['is_default_sales' => false]);
        $targetUser->update(['is_default_sales' => true]);
        return back()->with('success', "{$targetUser->name} berhasil diatur sebagai Penanggung Jawab Default.");
    }

    public function toggleUser(User $targetUser)
    {
        $targetUser->update(['is_active' => !$targetUser->is_active]);
        $status = $targetUser->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "{$targetUser->name} berhasil {$status}.");
    }

    // ==================== ROLES ====================

    public function roles()
    {
        $roles = Role::withCount('users')
            ->orderBy('name')
            ->get();
        return Inertia::render('Settings/Roles', compact('roles'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $data = [
            'tenant_id' => session('tenant_id'),
            'name' => $request->name,
            'description' => $request->description,
            'fee_type' => $request->input('fee_type', 'none'),
            'fee_persen' => $request->input('fee_persen', 0),
            'fee_fix' => $request->input('fee_fix', 0),
        ];

        foreach ($this->permKeys as $key) {
            $data[$key] = $request->boolean($key);
        }

        Role::create($data);
        return back()->with('success', 'Role berhasil ditambahkan.');
    }

    public function updateRole(Request $request, Role $role)
    {
        $data = [
            'name' => $request->input('name', $role->name),
            'description' => $request->input('description', $role->description),
            'fee_type' => $request->input('fee_type', $role->fee_type ?? 'none'),
            'fee_persen' => $request->input('fee_persen', $role->fee_persen ?? 0),
            'fee_fix' => $request->input('fee_fix', $role->fee_fix ?? 0),
        ];

        foreach ($this->permKeys as $key) {
            if ($request->has($key)) {
                $data[$key] = (bool) $request->input($key);
            }
        }

        $role->update($data);
        return back()->with('success', 'Role berhasil diperbarui.');
    }

    public function destroyRole(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Role masih dipakai oleh ' . $role->users()->count() . ' user.');
        }

        $role->delete();
        return back()->with('success', 'Role berhasil dihapus.');
    }

    // ==================== PROFIL PERUSAHAAN ====================

    public function profil()
    {
        $keys = ['company_nama', 'company_alamat', 'company_telepon', 'company_nomer_cs', 'company_email', 'company_website', 'company_logo', 'ppn_rate', 'bhp_uso_rate', 'admin_fee'];
        $configs = DB::table('billing_configs')
            ->where('tenant_id', session('tenant_id'))
            ->whereIn('key', $keys)
            ->pluck('value', 'key');
        return Inertia::render('Settings/Profil', compact('configs'));
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $allowedKeys = ['company_nama', 'company_alamat', 'company_telepon', 'company_nomer_cs', 'company_email', 'company_website', 'ppn_rate', 'bhp_uso_rate', 'admin_fee'];

        foreach ($allowedKeys as $key) {
            if ($request->has($key)) {
                DB::table('billing_configs')->updateOrInsert(
                    ['tenant_id' => session('tenant_id'), 'key' => $key],
                    ['value' => $request->input($key, '')]
                );
            }
        }

        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $filename = 'logo_' . session('tenant_id') . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/logos', $filename);
            
            DB::table('billing_configs')->updateOrInsert(
                ['tenant_id' => session('tenant_id'), 'key' => 'company_logo'],
                ['value' => Storage::url($path)]
            );
        }

        return back()->with('success', 'Profil Perusahaan berhasil disimpan.');
    }

    // ==================== TEMPLATE ====================

    public function template()
    {
        $keys = [
            'template_wa_tagihan', 'template_wa_lunas', 'template_wa_info',
            'template_invoice_58mm', 'template_invoice_a5'
        ];
        $configs = DB::table('billing_configs')
            ->where('tenant_id', session('tenant_id'))
            ->whereIn('key', $keys)
            ->pluck('value', 'key');
        return Inertia::render('Settings/Template', compact('configs'));
    }

    public function updateTemplate(Request $request)
    {
        $allowedKeys = [
            'template_wa_tagihan', 'template_wa_lunas', 'template_wa_info',
            'template_invoice_58mm', 'template_invoice_a5'
        ];

        foreach ($allowedKeys as $key) {
            if ($request->has($key)) {
                DB::table('billing_configs')->updateOrInsert(
                    ['tenant_id' => session('tenant_id'), 'key' => $key],
                    ['value' => $request->input($key, '')]
                );
            }
        }

        return back()->with('success', 'Pengaturan Template berhasil disimpan.');
    }

    public function dbPusat()
    {
        $configs = DB::table('billing_configs')
            ->where('tenant_id', session('tenant_id'))
            ->whereIn('key', [
                'db_pusat_software', 'db_pusat_sync_method', 'db_pusat_auth_type', 
                'db_pusat_url', 'db_pusat_token', 'db_pusat_username', 'db_pusat_password', 'db_pusat_type'
            ])
            ->pluck('value', 'key');
        return Inertia::render('Settings/DbPusat', compact('configs'));
    }

    public function updateDbPusat(Request $request)
    {
        $allowedKeys = [
            'db_pusat_software', 'db_pusat_sync_method', 'db_pusat_auth_type', 
            'db_pusat_url', 'db_pusat_token', 'db_pusat_username', 'db_pusat_password', 'db_pusat_type'
        ];

        foreach ($allowedKeys as $key) {
            if ($request->has($key)) {
                DB::table('billing_configs')->updateOrInsert(
                    ['tenant_id' => session('tenant_id'), 'key' => $key],
                    ['value' => $request->input($key, '')]
                );
            }
        }

        // Jalankan Pull setelah konfig disimpan
        try {
            $service = new \App\Services\DbPusatService();
            $service->pullPackages();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to pull packages on config update: " . $e->getMessage());
        }

        return back()->with('success', 'Pengaturan DB Pusat berhasil disimpan & Data Profil ditarik.');
    }

    // ==================== WA GATEWAY ====================

    public function waGateway()
    {
        $configs = DB::table('configs')
            ->whereIn('key', ['wa_gateway_url', 'wa_api_key', 'wa_sender_number', 'wa_message_template_tagihan', 'wa_message_template_lunas'])
            ->pluck('value', 'key');
        return Inertia::render('Settings/WaGateway', compact('configs'));
    }

    // ==================== CONFIG ROUTER ====================

    public function router()
    {
        $routers = Router::orderBy('name')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'name' => $r->name,
                    'host' => $r->host,
                    'port' => $r->port,
                    'ssh_port' => $r->ssh_port ?? 22,
                    'ftp_port' => $r->ftp_port ?? 21,
                    'username' => $r->username,
                    'is_active' => $r->is_active,
                    'use_ssl' => $r->use_ssl,
                    'auto_backup' => $r->auto_backup,
                    'use_radius' => $r->use_radius,
                    'radius_secret_masked' => $r->radius_secret
                        ? '***' . substr($r->getDecryptedRadiusSecret() ?? '', -4)
                        : null,
                    'description' => $r->description,
                ];
            });

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'data' => $routers]);
        }

        return Inertia::render('Settings/Router', compact('routers'));
    }

    public function storeRouter(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'host' => 'required|string|max:255',
            'username' => 'required|string|max:100',
            'password' => 'required|string',
            'port' => 'required|integer|min:1|max:65535',
            'ssh_port' => 'nullable|integer|min:1|max:65535',
            'ftp_port' => 'nullable|integer|min:1|max:65535',
            'auto_backup' => 'nullable|boolean',
            'use_radius' => 'nullable|boolean',
            'radius_secret' => 'nullable|string',
        ]);

        if ($request->boolean('use_radius') && empty($data['radius_secret'])) {
            return redirect()->back()->withErrors(['radius_secret' => 'RADIUS Secret wajib diisi jika FreeRADIUS aktif.']);
        }

        $data['tenant_id'] = session('tenant_id');
        $data['ssh_port'] = $data['ssh_port'] ?? 22;
        $data['ftp_port'] = $data['ftp_port'] ?? 21;
        $data['auto_backup'] = $request->boolean('auto_backup');
        $data['use_radius'] = $request->boolean('use_radius');
        $data['is_active'] = true;

        Router::create($data);
        return redirect()->back()->with('success', 'Router berhasil ditambahkan.');
    }

    public function updateRouter(Request $request, Router $router)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'host' => 'required|string|max:255',
            'username' => 'required|string|max:100',
            'password' => 'nullable|string',
            'port' => 'required|integer|min:1|max:65535',
            'ssh_port' => 'nullable|integer|min:1|max:65535',
            'ftp_port' => 'nullable|integer|min:1|max:65535',
            'auto_backup' => 'nullable|boolean',
            'use_radius' => 'nullable|boolean',
            'radius_secret' => 'nullable|string',
        ]);

        if ($request->boolean('use_radius') && empty($data['radius_secret'])) {
            return redirect()->back()->withErrors(['radius_secret' => 'RADIUS Secret wajib diisi jika FreeRADIUS aktif.']);
        }

        // Don't update password if not provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['ssh_port'] = $data['ssh_port'] ?? 22;
        $data['ftp_port'] = $data['ftp_port'] ?? 21;
        $data['auto_backup'] = $request->boolean('auto_backup');
        $data['use_radius'] = $request->boolean('use_radius');

        if (!$data['use_radius']) {
            $data['radius_secret'] = null;
        }

        $router->update($data);
        return redirect()->back()->with('success', 'Router berhasil diperbarui.');
    }

    public function destroyRouter(Router $router)
    {
        if ($router->customers()->count() > 0) {
            return redirect()->back()->with('error', 'Router masih dipakai oleh ' . $router->customers()->count() . ' pelanggan.');
        }

        $router->delete();
        return redirect()->back()->with('success', 'Router berhasil dihapus.');
    }

    public function toggleRouter(Router $router)
    {
        $router->update(['is_active' => !$router->is_active]);
        $status = $router->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json(['status' => 'success', 'message' => "Router berhasil {$status}."]);
    }

    public function testRouter(Router $router)
    {
        try {
            $apiFile = app_path('Services/MikroTik/RouterosAPI.php');
            if (!file_exists($apiFile)) {
                return response()->json(['status' => 'error', 'message' => 'Library RouterosAPI tidak ditemukan.']);
            }
            require_once $apiFile;

            $API = new \RouterosAPI();
            $API->port = $router->port;
            $API->timeout = 5;
            $API->attempts = 1;

            if ($API->connect($router->host, $router->username, $router->getDecryptedPassword())) {
                $identity = '(unknown)';
                $idOutput = @$API->comm('/system/identity/print');
                if (is_array($idOutput) && isset($idOutput[0]['name'])) {
                    $identity = $idOutput[0]['name'];
                }
                $API->disconnect();
                return response()->json([
                    'status' => 'success',
                    'message' => "Koneksi API ke \"{$router->name}\" berhasil. Identity: {$identity}",
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Gagal terhubung via API ke \"{$router->name}\" ({$router->host}:{$router->port}). Kredensial atau port mungkin salah.",
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => "Error: {$e->getMessage()}",
            ]);
        }
    }

    public function getRouterConfig(Router $router)
    {
        try {
            $service = new \App\Services\MikroTik\RouterConfigService();
            $configText = $service->fetchConfigFromRouterSelective($router);
            $configText = mb_convert_encoding($configText, 'UTF-8', 'UTF-8');
            return response()->json(['status' => 'success', 'config' => $configText]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function backupRouter(Router $router)
    {
        try {
            $service = new \App\Services\MikroTik\RouterConfigService();
            $configText = $service->fetchConfigFromRouterSelective($router);
            $configText = mb_convert_encoding($configText, 'UTF-8', 'UTF-8');
            
            if (empty(trim($configText))) {
                return response()->json(['status' => 'error', 'message' => 'Hasil export dari router kosong.']);
            }

            // Get router identity to use in backup name
            $identity = 'unknown';
            try {
                $apiFile = app_path('Services/MikroTik/RouterosAPI.php');
                require_once $apiFile;
                $API = new \RouterosAPI();
                $API->port = $router->port;
                $API->timeout = 5;
                $API->attempts = 1;
                if ($API->connect($router->host, $router->username, $router->getDecryptedPassword())) {
                    $idOutput = @$API->comm('/system/identity/print');
                    if (is_array($idOutput) && isset($idOutput[0]['name'])) {
                        $identity = $idOutput[0]['name'];
                    }
                    $API->disconnect();
                }
            } catch (\Throwable $e) {}

            $configName = $router->name . '-' . $identity . ' (' . now()->format('H:i/d-m-Y') . ')';
            
            $backup = RouterConfigBackup::create([
                'router_id' => $router->id,
                'config_name' => $configName,
                'config_text' => $configText,
            ]);

            $router->update(['last_backup_at' => now()]);

            // ─── Rotation: max 3, hapus yang lama ───
            $all = RouterConfigBackup::where('router_id', $router->id)
                ->orderByDesc('created_at')
                ->get();
            if ($all->count() > 3) {
                $toDelete = $all->slice(3);
                RouterConfigBackup::whereIn('id', $toDelete->pluck('id'))->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => "Backup \"{$configName}\" berhasil disimpan via SSH.",
                'id' => $backup->id,
                'config_name' => $backup->config_name,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function downloadBackup(Router $router, RouterConfigBackup $backup)
    {
        if ($backup->router_id !== $router->id) {
            abort(404);
        }

        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $backup->config_name) . '.rsc';
        return response($backup->config_text, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function listRouterBackups(Router $router)
    {
        $backups = RouterConfigBackup::where('router_id', $router->id)
            ->orderByDesc('created_at')
            ->get(['id', 'router_id', 'config_name', 'created_at']);

        return response()->json(['status' => 'success', 'data' => $backups]);
    }

    public function restoreRouterBackup(Router $router, RouterConfigBackup $backup)
    {
        if ($backup->router_id !== $router->id) {
            return response()->json(['status' => 'error', 'message' => 'Backup tidak cocok dengan router.'], 400);
        }

        return response()->json([
            'status' => 'success',
            'config' => $backup->config_text,
            'config_name' => $backup->config_name,
        ]);
    }

    // ==================== CONFIG SERVER GENIACS ====================

    public function server()
    {
        $servers = Server::where('type', 'geniacs')
            ->orderBy('name')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'host' => $s->host,
                    'port' => $s->port,
                    'username' => $s->username,
                    'is_active' => $s->is_active,
                ];
            });

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'data' => $servers]);
        }

        return Inertia::render('Settings/Server', compact('servers'));
    }

    public function storeServer(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string',
        ]);

        $data['tenant_id'] = session('tenant_id');
        $data['type'] = 'geniacs';
        $data['is_active'] = true;
        // Set required fields for server table that may have NOT NULL constraints
        $data['db_name'] = $data['db_name'] ?? 'geniacs';
        $data['db_username'] = $data['db_username'] ?? '';
        $data['db_password'] = $data['db_password'] ?? 'none';

        Server::create($data);
        return response()->json(['status' => 'success', 'message' => 'Server berhasil ditambahkan.']);
    }

    public function updateServer(Request $request, Server $server)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string',
        ]);

        // Don't update password if not provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $server->update($data);
        return response()->json(['status' => 'success', 'message' => 'Server berhasil diperbarui.']);
    }

    public function destroyServer(Server $server)
    {
        $server->delete();
        return response()->json(['status' => 'success', 'message' => 'Server berhasil dihapus.']);
    }

    public function toggleServer(Server $server)
    {
        $server->update(['is_active' => !$server->is_active]);
        $status = $server->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json(['status' => 'success', 'message' => "Server berhasil {$status}."]);
    }

    public function testServer(Server $server)
    {
        try {
            $host = $server->host;
            $port = $server->port;
            $baseUrl = "http://{$host}:{$port}";

            // Build HTTP context with optional basic auth
            $opts = [
                'http' => [
                    'method' => 'GET',
                    'timeout' => 8,
                    'ignore_errors' => true,
                    'header' => "Accept: application/json\r\n",
                ],
            ];

            $username = $server->username;
            $password = $server->getDecryptedPassword();
            if (!empty($username)) {
                $auth = base64_encode("{$username}:{$password}");
                $opts['http']['header'] .= "Authorization: Basic {$auth}\r\n";
            }

            $context = stream_context_create($opts);

            // 1) Query devices count
            $devicesUrl = "{$baseUrl}/devices/?limit=1";
            $response = @file_get_contents($devicesUrl, false, $context);

            if ($response === false) {
                // Fallback: try socket-level check
                $connection = @fsockopen($host, $port, $errno, $errstr, 5);
                if ($connection) {
                    fclose($connection);
                    return response()->json([
                        'status' => 'warning',
                        'message' => "Port {$port} terbuka di \"{$server->name}\" ({$host}), tetapi GenieACS API tidak merespons pada endpoint /devices/. Pastikan port {$port} adalah port NBI (default 7557).",
                    ]);
                }
                return response()->json([
                    'status' => 'error',
                    'message' => "Gagal terhubung ke \"{$server->name}\" ({$host}:{$port}). Server tidak dapat dijangkau.",
                ]);
            }

            // Parse response headers for HTTP status
            $httpCode = 200;
            if (isset($http_response_header) && is_array($http_response_header)) {
                foreach ($http_response_header as $header) {
                    if (preg_match('/^HTTP\/\d\.?\d?\s+(\d{3})/', $header, $m)) {
                        $httpCode = (int) $m[1];
                    }
                }
            }

            if ($httpCode === 401) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Autentikasi gagal (HTTP 401). Username/password GenieACS salah.",
                ]);
            }

            if ($httpCode >= 400) {
                return response()->json([
                    'status' => 'error',
                    'message' => "GenieACS API merespons dengan HTTP {$httpCode}.",
                ]);
            }

            $devices = json_decode($response, true);
            $deviceCount = is_array($devices) ? count($devices) : 0;

            // 2) Try to get total count via /devices/?limit=0 with count header
            $countUrl = "{$baseUrl}/devices/?limit=0";
            $countResponse = @file_get_contents($countUrl, false, $context);
            $totalDevices = null;
            if (isset($http_response_header) && is_array($http_response_header)) {
                foreach ($http_response_header as $header) {
                    if (stripos($header, 'X-Total-Count:') !== false || stripos($header, 'Total:') !== false) {
                        preg_match('/:\s*(\d+)/', $header, $cm);
                        if (isset($cm[1])) $totalDevices = (int) $cm[1];
                    }
                }
            }

            $msg = "Koneksi ke GenieACS \"{$server->name}\" ({$host}:{$port}) berhasil.";
            if ($totalDevices !== null) {
                $msg .= " Total perangkat terdaftar: {$totalDevices}.";
            } elseif ($deviceCount > 0) {
                $msg .= " Terdeteksi {$deviceCount} perangkat (sample).";
            } else {
                $msg .= " API merespons — belum ada perangkat terdaftar.";
            }

            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'device_count' => $totalDevices ?? $deviceCount,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => "Error: {$e->getMessage()}",
            ]);
        }
    }

    // ==================== ACS PARAMETER MAPPING ====================

    /**
     * Default ACS parameter keys with labels and descriptions
     */
    private function getAcsParamDefinitions(): array
    {
        return [
            ['key' => 'acs_param_pppoe_username',   'label' => 'PPPoE Username',        'description' => 'Virtual Parameter untuk PPPoE Username pelanggan'],
            ['key' => 'acs_param_rx_power',          'label' => 'RX Power',              'description' => 'Virtual Parameter untuk RX Power (signal strength ONU)'],
            ['key' => 'acs_param_temperature',       'label' => 'Temperature',           'description' => 'Virtual Parameter untuk suhu perangkat ONU'],
            ['key' => 'acs_param_active_devices',    'label' => 'Active Devices',        'description' => 'Virtual Parameter untuk jumlah perangkat aktif di bawah ONU'],
            ['key' => 'acs_param_pon_mode',          'label' => 'PON Mode',              'description' => 'Virtual Parameter untuk mode PON (GPON/EPON/XGS-PON)'],
            ['key' => 'acs_param_ssid_2ghz',         'label' => 'SSID 2.4 GHz',         'description' => 'Virtual Parameter untuk nama SSID WiFi 2.4 GHz'],
            ['key' => 'acs_param_pass_2ghz',         'label' => 'Password 2.4 GHz',     'description' => 'Virtual Parameter untuk password WiFi 2.4 GHz'],
            ['key' => 'acs_param_ssid_5ghz',         'label' => 'SSID 5 GHz',           'description' => 'Virtual Parameter untuk nama SSID WiFi 5 GHz'],
            ['key' => 'acs_param_pass_5ghz',         'label' => 'Password 5 GHz',       'description' => 'Virtual Parameter untuk password WiFi 5 GHz'],
            ['key' => 'acs_param_product_class',     'label' => 'Product Class',         'description' => 'Path TR-069 untuk Product Class perangkat'],
            ['key' => 'acs_param_serial_number',     'label' => 'Serial Number',         'description' => 'Path TR-069 untuk DeviceID.SerialNumber'],
            ['key' => 'acs_param_manufacturer',       'label' => 'Manufacturer',          'description' => 'Path TR-069 untuk DeviceID.Manufacturer'],
        ];
    }

    public function getAcsParams()
    {
        $definitions = $this->getAcsParamDefinitions();
        $keys = array_column($definitions, 'key');

        $saved = DB::table('billing_configs')
            ->where('tenant_id', session('tenant_id'))
            ->whereIn('key', $keys)
            ->pluck('value', 'key');

        $params = array_map(function ($def) use ($saved) {
            $def['value'] = $saved[$def['key']] ?? '';
            return $def;
        }, $definitions);

        return response()->json(['status' => 'success', 'data' => $params]);
    }

    public function saveAcsParams(Request $request)
    {
        $definitions = $this->getAcsParamDefinitions();
        $allowedKeys = array_column($definitions, 'key');

        $data = $request->validate([
            'params' => 'required|array',
            'params.*.key' => 'required|string|in:' . implode(',', $allowedKeys),
            'params.*.value' => 'nullable|string|max:500',
        ]);

        foreach ($data['params'] as $param) {
            DB::table('billing_configs')->updateOrInsert(
                ['tenant_id' => session('tenant_id'), 'key' => $param['key']],
                ['value' => $param['value'] ?? '']
            );
        }

        return response()->json(['status' => 'success', 'message' => 'Konfigurasi parameter ACS berhasil disimpan.']);
    }

    // ==================== BACKUP ====================

    public function backup()
    {
        return Inertia::render('Settings/Backup');
    }

    // ==================== LOG SISTEM ====================

    public function logSistem()
    {
        $logs = DB::table('audit_logs')
            ->where('tenant_id', session('tenant_id'))
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();
        return Inertia::render('Settings/LogSistem', compact('logs'));
    }

    public function radiusServer()
    {
        $servers = Server::whereIn('type', ['freeradius', 'daloradius_api', 'radiusdesk_api'])
            ->orderBy('name')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'type' => $s->type,
                    'host' => $s->host,
                    'port' => $s->port,
                    'db_name' => $s->db_name,
                    'username' => $s->username,
                    'api_endpoint' => $s->api_endpoint,
                    'has_api_token' => !empty($s->api_token),
                    'is_active' => $s->is_active,
                    'routers_count' => Router::where('radius_server_id', $s->id)->count(),
                ];
            });

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'data' => $servers]);
        }
        return Inertia::render('Settings/RadiusServer');
    }

    public function storeRadiusServer(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:freeradius,daloradius_api,radiusdesk_api',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'db_name' => 'nullable|string|max:100',
            'username' => 'nullable|string|max:100',
            'db_password' => 'nullable|string',
            'api_endpoint' => 'nullable|string|max:255',
            'api_token' => 'nullable|string',
        ]);

        $data['tenant_id'] = session('tenant_id');
        $data['is_active'] = true;
        
        if ($data['type'] === 'freeradius') {
            $data['db_username'] = $data['username'] ?? '';
            $data['password'] = $data['db_password'] ?? 'none';
        } else {
            // Provide defaults for API so it doesn't violate NOT NULL constraints
            $data['host'] = $data['api_endpoint'] ?? 'api';
            $data['db_username'] = 'api_user';
            $data['password'] = 'none';
        }

        Server::create($data);
        return response()->json(['status' => 'success', 'message' => 'Server RADIUS berhasil ditambahkan.']);
    }

    public function updateRadiusServer(Request $request, Server $server)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:freeradius,daloradius_api,radiusdesk_api',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'db_name' => 'nullable|string|max:100',
            'username' => 'nullable|string|max:100',
            'db_password' => 'nullable|string',
            'api_endpoint' => 'nullable|string|max:255',
            'api_token' => 'nullable|string',
        ]);

        if (empty($data['db_password'])) unset($data['db_password']);
        if (empty($data['api_token'])) unset($data['api_token']);

        if ($data['type'] === 'freeradius') {
            $data['db_username'] = $data['username'] ?? '';
            if (isset($data['db_password'])) $data['password'] = $data['db_password'];
        } else {
            $data['host'] = $data['api_endpoint'] ?? 'api';
        }

        $server->update($data);
        return response()->json(['status' => 'success', 'message' => 'Server RADIUS berhasil diperbarui.']);
    }

    public function destroyRadiusServer(Server $server)
    {
        // Cek apakah ada router yang masih menggunakan server ini
        $routerCount = Router::where('radius_server_id', $server->id)->count();
        if ($routerCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Tidak bisa hapus — masih digunakan oleh {$routerCount} router.",
            ]);
        }

        $server->delete();
        return response()->json(['status' => 'success', 'message' => 'Server RADIUS berhasil dihapus.']);
    }

    public function toggleRadiusServer(Server $server)
    {
        $server->update(['is_active' => !$server->is_active]);
        $status = $server->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json(['status' => 'success', 'message' => "Server RADIUS berhasil {$status}."]);
    }

    public function testRadiusServer(Server $server)
    {
        $radiusService = app(\App\Services\RadiusService::class);
        $result = $radiusService->testConnection($server);
        return response()->json($result);
    }
}
