<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IsolirController extends Controller
{
    public function __construct(private PermissionService $permissionService) {}

    public function index(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        $tab = $request->query('tab', 'isolated'); // 'isolated' or 'candidates'
        $search = strtolower($request->query('search', ''));
        $perPage = $request->query('per_page', 15);
        if ($perPage === 'all') {
            $perPage = 999999;
        }

        $isolatedQuery = Customer::with(['package', 'area', 'sales'])->where('is_isolated', true);
        $candidatesQuery = Customer::with(['package', 'area', 'sales'])->where('status', 'active')->where('is_isolated', false);

        if ($search) {
            $isolatedQuery->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(username) LIKE ?', ["%{$search}%"]);
            });
            $candidatesQuery->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(username) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('area_id')) {
            $isolatedQuery->where('area_id', $request->area_id);
            $candidatesQuery->where('area_id', $request->area_id);
        }

        $isolated = $isolatedQuery->orderBy('isolated_since', 'desc')->paginate($perPage, ['*'], 'page_isolated')->withQueryString();
        $candidates = $candidatesQuery->orderBy('name')->paginate($perPage, ['*'], 'page_candidates')->withQueryString();

        $stats = [
            'total_isolated' => Customer::where('is_isolated', true)->count(),
            'total_active' => Customer::where('status', 'active')->where('is_isolated', false)->count(),
        ];

        $filters = $request->only(['search', 'area_id', 'per_page', 'tab']);
        $areas = \App\Models\Area::orderBy('name')->get();

        return Inertia::render('Cust/Isolir/Index', compact('capabilities', 'isolated', 'candidates', 'stats', 'filters', 'areas', 'tab'));
    }

    public function isolate(Request $request, Customer $customer, \App\Services\MikroTik\MikroTikService $mikrotik, \App\Services\GeniAcsService $acs, \App\Services\RadiusService $radiusService)
    {
        // 1. If using Mikrotik (Local Auth)
        if ($customer->router_id && $customer->username && !$customer->server_id) {
            $packageRouter = \DB::table('package_routers')->where('package_id', $customer->package_id)->where('router_id', $customer->router_id)->first();
            $isolirProfile = $packageRouter?->isolir_profile ?? 'isolir';
            
            $mikrotik->changeSecretProfile($customer->router, $customer->username, $isolirProfile);
        }

        // 2. If using RADIUS
        if ($customer->server_id && $customer->username) {
            $packageServer = \DB::table('package_servers')->where('package_id', $customer->package_id)->where('server_id', $customer->server_id)->first();
            $isolirGroup = $packageServer?->radius_isolir_group ?? 'isolir';
            
            if ($customer->server) {
                $radiusService->connectTo($customer->server);
                $radiusService->updateUser($customer->username, null, $isolirGroup);
            }

            // Kick active session if Mikrotik router is known
            if ($customer->router_id && $customer->router) {
                $mikrotik->kickActiveSession($customer->router, $customer->username);
            }
        }

        // Reboot via ACS if username exists
        if ($customer->username) {
            $acs->rebootDevice($customer->username);
        }

        $customer->update(['is_isolated' => true, 'isolated_since' => now()]);
        return back()->with('success', "{$customer->name} berhasil diisolir.");
    }

    public function release(Request $request, Customer $customer, \App\Services\MikroTik\MikroTikService $mikrotik, \App\Services\GeniAcsService $acs, \App\Services\RadiusService $radiusService)
    {
        // 1. If using Mikrotik (Local Auth)
        if ($customer->router_id && $customer->username && !$customer->server_id) {
            $packageRouter = \DB::table('package_routers')->where('package_id', $customer->package_id)->where('router_id', $customer->router_id)->first();
            $normalProfile = $packageRouter?->pppoe_profile ?? 'default';
            
            $mikrotik->changeSecretProfile($customer->router, $customer->username, $normalProfile);
        }

        // 2. If using RADIUS
        if ($customer->server_id && $customer->username) {
            $packageServer = \DB::table('package_servers')->where('package_id', $customer->package_id)->where('server_id', $customer->server_id)->first();
            $normalGroup = $packageServer?->radius_group ?? 'default';
            
            if ($customer->server) {
                $radiusService->connectTo($customer->server);
                $radiusService->updateUser($customer->username, null, $normalGroup);
            }

            // Kick active session
            if ($customer->router_id && $customer->router) {
                $mikrotik->kickActiveSession($customer->router, $customer->username);
            }
        }

        // Reboot via ACS if username exists
        if ($customer->username) {
            $acs->rebootDevice($customer->username);
        }

        $customer->update(['is_isolated' => false, 'isolated_since' => null]);
        return back()->with('success', "{$customer->name} berhasil dilepas dari isolir.");
    }

    public function batchIsolate(Request $request, \App\Services\MikroTik\MikroTikService $mikrotik, \App\Services\GeniAcsService $acs, \App\Services\RadiusService $radiusService)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customers,id',
        ]);

        $customers = Customer::with(['router', 'server'])->whereIn('id', $request->ids)->get();
        foreach ($customers as $customer) {
            if ($customer->router_id && $customer->username && !$customer->server_id) {
                $packageRouter = \DB::table('package_routers')->where('package_id', $customer->package_id)->where('router_id', $customer->router_id)->first();
                $isolirProfile = $packageRouter?->isolir_profile ?? 'isolir';
                
                $mikrotik->changeSecretProfile($customer->router, $customer->username, $isolirProfile);
            }

            if ($customer->server_id && $customer->username) {
                $packageServer = \DB::table('package_servers')->where('package_id', $customer->package_id)->where('server_id', $customer->server_id)->first();
                $isolirGroup = $packageServer?->radius_isolir_group ?? 'isolir';
                
                if ($customer->server) {
                    $radiusService->connectTo($customer->server);
                    $radiusService->updateUser($customer->username, null, $isolirGroup);
                }
                
                if ($customer->router_id && $customer->router) {
                    $mikrotik->kickActiveSession($customer->router, $customer->username);
                }
            }

            if ($customer->username) {
                $acs->rebootDevice($customer->username);
            }

            $customer->update(['is_isolated' => true, 'isolated_since' => now()]);
        }

        return back()->with('success', count($customers) . ' pelanggan berhasil diisolir.');
    }

    public function batchRelease(Request $request, \App\Services\MikroTik\MikroTikService $mikrotik, \App\Services\GeniAcsService $acs, \App\Services\RadiusService $radiusService)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customers,id',
        ]);

        $customers = Customer::with(['router', 'server'])->whereIn('id', $request->ids)->get();
        foreach ($customers as $customer) {
            if ($customer->router_id && $customer->username && !$customer->server_id) {
                $packageRouter = \DB::table('package_routers')->where('package_id', $customer->package_id)->where('router_id', $customer->router_id)->first();
                $normalProfile = $packageRouter?->pppoe_profile ?? 'default';
                
                $mikrotik->changeSecretProfile($customer->router, $customer->username, $normalProfile);
            }

            if ($customer->server_id && $customer->username) {
                $packageServer = \DB::table('package_servers')->where('package_id', $customer->package_id)->where('server_id', $customer->server_id)->first();
                $normalGroup = $packageServer?->radius_group ?? 'default';
                
                if ($customer->server) {
                    $radiusService->connectTo($customer->server);
                    $radiusService->updateUser($customer->username, null, $normalGroup);
                }

                if ($customer->router_id && $customer->router) {
                    $mikrotik->kickActiveSession($customer->router, $customer->username);
                }
            }

            if ($customer->username) {
                $acs->rebootDevice($customer->username);
            }

            $customer->update(['is_isolated' => false, 'isolated_since' => null]);
        }

        return back()->with('success', count($customers) . ' pelanggan berhasil dilepas dari isolir.');
    }
}
