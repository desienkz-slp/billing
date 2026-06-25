<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Package;
use App\Models\PackageRouter;
use App\Models\Router;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function __construct(private PermissionService $permissionService) {}

    // ===== USERS =====
    public function users(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        $users = User::with('role')->orderBy('name')->get();
        $roles = \App\Models\Role::orderBy('name')->get();

        return Inertia::render('Settings/Users', compact('capabilities', 'users', 'roles'));
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
            'fee_persen' => 'nullable|numeric|min:0|max:100',
            'fee_fix' => 'nullable|numeric|min:0',
        ]);

        $data['uuid'] = (string) Str::uuid();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;
        $data['fee_persen'] = $data['fee_persen'] ?? 0;
        $data['fee_fix'] = $data['fee_fix'] ?? 0;

        User::create($data);
        return back()->with('success', "User {$data['name']} berhasil ditambahkan.");
    }

    public function updateUser(Request $request, User $targetUser)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'fee_persen' => 'nullable|numeric|min:0|max:100',
            'fee_fix' => 'nullable|numeric|min:0',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['fee_persen'] = $data['fee_persen'] ?? 0;
        $data['fee_fix'] = $data['fee_fix'] ?? 0;

        $targetUser->update($data);

        return back()->with('success', "User {$targetUser->name} berhasil diperbarui.");
    }

    public function toggleUser(User $targetUser)
    {
        $targetUser->update(['is_active' => !$targetUser->is_active]);
        $status = $targetUser->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "{$targetUser->name} berhasil {$status}.");
    }

    public function setDefaultSales(User $targetUser)
    {
        // Reset all other users in this tenant
        User::where('tenant_id', $targetUser->tenant_id)->update(['is_default_sales' => false]);
        
        // Set this user as default
        $targetUser->update(['is_default_sales' => true]);
        
        return back()->with('success', "{$targetUser->name} berhasil diatur sebagai Penanggung Jawab Default.");
    }

    // ===== PACKAGES =====
    public function packages(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        $packages = Package::withCount('customers')
            ->with('packageRouters.router')
            ->with('packageServers.server')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $routers = Router::where('is_active', true)
            ->with('pppoeProfiles')
            ->orderBy('name')
            ->get();

        $servers = \App\Models\Server::where('is_active', true)
            ->whereIn('type', ['freeradius', 'upluk_upluk_api'])
            ->orderBy('name')
            ->get();

        $db_pusat_profiles = [];
        $radiusServer = \App\Models\Server::whereIn('type', ['freeradius', 'upluk_upluk_api'])->where('is_active', true)->first();
        if ($radiusServer) {
            $radiusService = app(\App\Services\RadiusService::class);
            $radiusService->connectTo($radiusServer);
            if (method_exists($radiusService, 'listGroups')) {
                $db_pusat_profiles = collect($radiusService->listGroups())->pluck('groupname')->unique()->values()->toArray();
            } else {
                try {
                    $db_pusat_profiles = \App\Models\Radius\RadGroupReply::select('groupname')->distinct()->pluck('groupname')->toArray();
                } catch (\Throwable $e) {}
            }
        }

        return Inertia::render('Settings/Packages', compact('capabilities', 'packages', 'routers', 'servers', 'db_pusat_profiles'));
    }

    public function routerProfiles(Router $router)
    {
        // Try DB first
        $profiles = $router->pppoeProfiles()->orderBy('name')->get(['id', 'name', 'rate_limit']);

        // If empty, fetch live from MikroTik
        if ($profiles->isEmpty()) {
            try {
                $mikrotik = new \App\Services\MikroTik\MikroTikService();
                $liveProfiles = $mikrotik->getPppoeProfiles($router);
                $profiles = collect($liveProfiles)->map(function ($p) {
                    return [
                        'name' => $p['name'] ?? 'unknown',
                        'rate_limit' => $p['rate-limit'] ?? null,
                    ];
                })->values();
            } catch (\Throwable $e) {
                $profiles = collect([]);
            }
        }

        return response()->json($profiles);
    }

    public function storePackage(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'speed' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
            'pppoe_profile' => 'nullable|string|max:100',
            'isolir_profile' => 'nullable|string|max:100',
            'radius_group' => 'nullable|string|max:100',
            'radius_group_isolir' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'router_profiles' => 'nullable|array',
            'server_profiles' => 'nullable|array',
        ]);

        $routerProfiles = $data['router_profiles'] ?? [];
        $serverProfiles = $data['server_profiles'] ?? [];
        unset($data['router_profiles'], $data['server_profiles']);
        $data['uuid'] = (string) Str::uuid();
        $data['tenant_id'] = session('tenant_id');

        $package = Package::create($data);

        foreach ($routerProfiles as $routerId => $profileData) {
            if (!isset($profileData['enabled'])) continue;
            PackageRouter::create([
                'tenant_id' => session('tenant_id'),
                'package_id' => $package->id,
                'router_id' => $routerId,
                'pppoe_profile' => $profileData['pppoe_profile'] ?? null,
                'isolir_profile' => $profileData['isolir_profile'] ?? null,
            ]);
        }

        foreach ($serverProfiles as $serverId => $profileData) {
            if (!isset($profileData['enabled'])) continue;
            \App\Models\PackageServer::create([
                'tenant_id' => session('tenant_id'),
                'package_id' => $package->id,
                'server_id' => $serverId,
                'radius_group' => $profileData['radius_group'] ?? null,
                'radius_isolir_group' => $profileData['radius_isolir_group'] ?? null,
            ]);
        }

        return back()->with('success', "Paket {$data['name']} berhasil ditambahkan.");
    }

    public function updatePackage(Request $request, Package $package)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'speed' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
            'pppoe_profile' => 'nullable|string|max:100',
            'isolir_profile' => 'nullable|string|max:100',
            'radius_group' => 'nullable|string|max:100',
            'radius_group_isolir' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'router_profiles' => 'nullable|array',
            'server_profiles' => 'nullable|array',
            'is_active' => 'nullable',
        ]);

        $routerProfiles = $data['router_profiles'] ?? [];
        $serverProfiles = $data['server_profiles'] ?? [];
        unset($data['router_profiles'], $data['server_profiles']);
        $data['is_active'] = $request->has('is_active');

        $package->update($data);

        // Sync router assignments
        PackageRouter::where('package_id', $package->id)->delete();
        foreach ($routerProfiles as $routerId => $profileData) {
            if (!isset($profileData['enabled'])) continue;
            PackageRouter::create([
                'tenant_id' => session('tenant_id'),
                'package_id' => $package->id,
                'router_id' => $routerId,
                'pppoe_profile' => $profileData['pppoe_profile'] ?? null,
                'isolir_profile' => $profileData['isolir_profile'] ?? null,
            ]);
        }

        // Sync server assignments
        \App\Models\PackageServer::where('package_id', $package->id)->delete();
        foreach ($serverProfiles as $serverId => $profileData) {
            if (!isset($profileData['enabled'])) continue;
            \App\Models\PackageServer::create([
                'tenant_id' => session('tenant_id'),
                'package_id' => $package->id,
                'server_id' => $serverId,
                'radius_group' => $profileData['radius_group'] ?? null,
                'radius_isolir_group' => $profileData['radius_isolir_group'] ?? null,
            ]);
        }

        return back()->with('success', "Paket {$package->name} berhasil diupdate.");
    }

    public function destroyPackage(Package $package)
    {
        if ($package->customers()->count() > 0) {
            return back()->with('error', "Paket {$package->name} tidak bisa dihapus karena masih memiliki {$package->customers()->count()} pelanggan.");
        }

        $name = $package->name;
        PackageRouter::where('package_id', $package->id)->delete();
        \App\Models\PackageServer::where('package_id', $package->id)->delete();
        $package->delete();

        return back()->with('success', "Paket {$name} berhasil dihapus.");
    }

    // ===== AREAS =====
    public function areas(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        $areas = Area::withCount('customers')->orderBy('name')->get();

        return Inertia::render('Settings/Areas', compact('capabilities', 'areas'));
    }

    public function storeArea(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);
        $data['uuid'] = (string) Str::uuid();
        Area::create($data);
        return back()->with('success', "Area {$data['name']} berhasil ditambahkan.");
    }

    // ===== ODP =====
    public function odps(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        $odps = \App\Models\Odp::with('area')->withCount('customers')->orderBy('name')->get();
        $areas = Area::orderBy('name')->get();

        return Inertia::render('Settings/Odps', compact('capabilities', 'odps', 'areas'));
    }

    public function storeOdp(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'area_id' => 'required|exists:areas,id',
            'capacity' => 'required|integer|min:1',
        ]);
        $data['uuid'] = (string) Str::uuid();
        $data['tenant_id'] = $request->user()->tenant_id;
        \App\Models\Odp::create($data);
        return back()->with('success', "ODP {$data['name']} berhasil ditambahkan.");
    }

    public function destroyOdp(\App\Models\Odp $odp)
    {
        $name = $odp->name;
        $odp->delete();
        return back()->with('success', "ODP {$name} berhasil dihapus.");
    }

    // ===== ODC =====
    public function odcs(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        $odcs = \App\Models\Odc::with('area')->withCount('odps')->orderBy('name')->get();
        $areas = Area::orderBy('name')->get();

        return Inertia::render('Settings/Odcs', compact('capabilities', 'odcs', 'areas'));
    }

    public function storeOdc(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'area_id' => 'required|exists:areas,id',
            'capacity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);
        $data['tenant_id'] = $request->user()->tenant_id;
        \App\Models\Odc::create($data);
        return back()->with('success', "ODC {$data['name']} berhasil ditambahkan.");
    }

    public function updateOdc(Request $request, \App\Models\Odc $odc)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'area_id' => 'required|exists:areas,id',
            'capacity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);
        $odc->update($data);
        return back()->with('success', "ODC {$odc->name} berhasil diperbarui.");
    }

    public function destroyOdc(\App\Models\Odc $odc)
    {
        $odpCount = $odc->odps()->count();
        if ($odpCount > 0) {
            return back()->with('error', "Tidak bisa hapus ODC {$odc->name} — masih punya {$odpCount} ODP terhubung.");
        }
        $name = $odc->name;
        $odc->delete();
        return back()->with('success', "ODC {$name} berhasil dihapus.");
    }

    // ===== TEMPLATES =====
    public function templateTagihan(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        return Inertia::render('Settings/TemplateTagihan', compact('capabilities'));
    }

    public function templateNota(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        return Inertia::render('Settings/TemplateNota', compact('capabilities'));
    }
}
