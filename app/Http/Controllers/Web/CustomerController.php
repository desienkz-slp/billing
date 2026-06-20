<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Area;
use App\Models\Package;
use App\Models\Router;
use App\Models\Server;
use App\Models\Odp;
use App\Models\User;
use App\Events\CustomerCreated;
use App\Models\CustomerCoordinate;
use App\Services\RadiusApiService;
use App\Services\RadiusService;
use App\Services\MikroTik\MikroTikService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with(['package', 'area', 'odp', 'router', 'server', 'sales'])->latest();

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(username) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('sales_id')) {
            $query->where('sales_id', $request->sales_id);
        }

        $perPage = 15;
        if ($request->filled('per_page')) {
            if ($request->per_page === 'all') {
                $perPage = $query->count() > 0 ? $query->count() : 1;
            } else {
                $perPage = (int) $request->per_page;
            }
        }

        $customers = $query->paginate($perPage)->withQueryString();
        
        $filters = $request->only(['search', 'area_id', 'sales_id', 'per_page']);

        $areas = Area::orderBy('name')->get();
        $packages = Package::orderBy('name')->get();
        $routers = Router::where('is_active', true)->get();
        $servers = Server::where('is_active', true)->get();
        $odps = Odp::orderBy('name')->get();
        $users = User::where('is_active', true)->whereHas('customers')->orderBy('name')->get();
        $defaultSalesId = User::where('is_active', true)->where('is_default_sales', true)->value('id');

        return Inertia::render('Cust/Customers/Index', compact('customers', 'areas', 'packages', 'routers', 'servers', 'odps', 'users', 'defaultSalesId', 'filters'));
    }

    public function create(): Response
    {
        $areas = Area::orderBy('name')->get();
        $packages = Package::orderBy('name')->get();
        $routers = Router::where('is_active', true)->get();
        $servers = Server::where('is_active', true)->get();
        $odps = Odp::orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        $defaultSalesId = User::where('is_active', true)->where('is_default_sales', true)->value('id');

        return Inertia::render('Cust/Customers/Create', compact('areas', 'packages', 'routers', 'servers', 'odps', 'users', 'defaultSalesId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:100',
            'password_pppoe' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'package_id' => 'required|exists:packages,id',
            'area_id' => 'required|exists:areas,id',
            'router_id' => 'nullable|exists:routers,id',
            'server_id' => 'nullable|exists:servers,id',
            'odp_id' => 'nullable|exists:odps,id',
            'sales_id' => 'required|exists:users,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'jenis_bayar' => 'required|in:prabayar,pascabayar',
            'status' => 'required|in:active,inactive',
            'max_tunggakan' => 'required|integer|min:0|max:12',
            'registration_date' => 'nullable|date',
            'billing_date' => 'required|integer|min:1|max:31',
            'tgl_isolir' => 'nullable|integer|min:1|max:31',
            'diskon' => 'nullable|numeric|min:0',
            'tambahan_layanan' => 'nullable|string|max:255',
            'deskripsi_layanan' => 'nullable|string',
            'custom_profile' => 'nullable|string',
            'custom_group' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['auto_isolir'] = $request->boolean('auto_isolir');
        $validated['pakai_ppn'] = $request->boolean('pakai_ppn');
        $validated['pakai_bhp'] = $request->boolean('pakai_bhp');
        $validated['pakai_admin'] = $request->boolean('pakai_admin');
        $validated['auto_wa_tagihan'] = $request->boolean('auto_wa_tagihan');
        $validated['sync_db_pusat'] = $request->boolean('sync_db_pusat');

        $metadata = [];
        if ($request->filled('custom_profile')) $metadata['custom_profile'] = $request->custom_profile;
        if ($request->filled('custom_group')) $metadata['custom_group'] = $request->custom_group;

        $storeData = collect($validated)->except(['latitude', 'longitude', 'custom_profile', 'custom_group'])->toArray();
        if (!empty($metadata)) {
            $storeData['metadata'] = $metadata;
        }

        $customer = Customer::create($storeData);

        if ($request->filled('latitude') || $request->filled('longitude')) {
            CustomerCoordinate::create([
                'customer_id' => $customer->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        }

        if ($request->boolean('create_pppoe')) {
            event(new CustomerCreated($customer, true));
        }

        if ($customer->sync_db_pusat) {
            app(RadiusApiService::class)->pushCustomer($customer);
        }

        return redirect()->route('cust.customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        $areas = Area::orderBy('name')->get();
        $packages = Package::orderBy('name')->get();
        $routers = Router::where('is_active', true)->get();
        $servers = Server::where('is_active', true)->get();
        $odps = Odp::orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        $customer->load('coordinate');

        return Inertia::render('Cust/Customers/Edit', compact('customer', 'areas', 'packages', 'routers', 'servers', 'odps', 'users'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:100',
            'password_pppoe' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'package_id' => 'required|exists:packages,id',
            'area_id' => 'required|exists:areas,id',
            'router_id' => 'nullable|exists:routers,id',
            'server_id' => 'nullable|exists:servers,id',
            'odp_id' => 'nullable|exists:odps,id',
            'sales_id' => 'required|exists:users,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'jenis_bayar' => 'required|in:prabayar,pascabayar',
            'status' => 'required|in:active,inactive',
            'max_tunggakan' => 'required|integer|min:0|max:12',
            'registration_date' => 'nullable|date',
            'billing_date' => 'required|integer|min:1|max:31',
            'tgl_isolir' => 'nullable|integer|min:1|max:31',
            'diskon' => 'nullable|numeric|min:0',
            'tambahan_layanan' => 'nullable|string|max:255',
            'deskripsi_layanan' => 'nullable|string',
            'custom_profile' => 'nullable|string',
            'custom_group' => 'nullable|string',
        ]);

        $validated['auto_isolir'] = $request->boolean('auto_isolir');
        $validated['pakai_ppn'] = $request->boolean('pakai_ppn');
        $validated['pakai_bhp'] = $request->boolean('pakai_bhp');
        $validated['pakai_admin'] = $request->boolean('pakai_admin');
        $validated['auto_wa_tagihan'] = $request->boolean('auto_wa_tagihan');
        $validated['sync_db_pusat'] = $request->boolean('sync_db_pusat');

        $metadata = [];
        if ($request->filled('custom_profile')) $metadata['custom_profile'] = $request->custom_profile;
        if ($request->filled('custom_group')) $metadata['custom_group'] = $request->custom_group;

        $updateData = collect($validated)->except(['latitude', 'longitude', 'custom_profile', 'custom_group'])->toArray();
        if (!empty($metadata)) {
            $existingMetadata = $customer->metadata ?? [];
            $updateData['metadata'] = array_merge($existingMetadata, $metadata);
        }

        $customer->update($updateData);

        if ($request->filled('latitude') || $request->filled('longitude')) {
            CustomerCoordinate::updateOrCreate(
                ['customer_id' => $customer->id],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]
            );
        }

        if ($customer->sync_db_pusat) {
            app(RadiusApiService::class)->pushCustomer($customer);
        }

        return redirect()->route('cust.customers.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }



    public function show($id)
    {
        $customer = Customer::with(['package', 'area', 'router', 'server', 'odp', 'sales', 'coordinate'])->find($id);
        if (!$customer) {
            return redirect()->route('cust.customers.index')->with('error', 'Customer not found');
        }

        return Inertia::render('Cust/Customers/Show', compact('customer'));
    }

    public function searchPppoeUsers()
    {
        $users = [];

        // 1. Fetch from FreeRADIUS
        $radiusServer = Server::where('type', 'freeradius')->where('is_active', true)->first();
        if ($radiusServer) {
            $radiusService = app(RadiusService::class);
            $radiusUsers = $radiusService->listUsers();
            foreach ($radiusUsers as $ru) {
                $users[] = [
                    'source' => 'FreeRADIUS DB',
                    'source_type' => 'radius',
                    'source_id' => $radiusServer->id,
                    'username' => $ru->username,
                    'password' => $ru->password,
                    'profile' => $ru->profile,
                ];
            }
        }

        // 2. Fetch from active MikroTik Routers
        $mikrotikService = app(MikroTikService::class);
        $routers = Router::where('is_active', true)->get();
        foreach ($routers as $router) {
            $secrets = $mikrotikService->getPppoeSecrets($router);
            foreach ($secrets as $sec) {
                $users[] = [
                    'source' => 'MikroTik (' . $router->name . ')',
                    'source_type' => 'router',
                    'source_id' => $router->id,
                    'username' => $sec['name'] ?? '',
                    'password' => $sec['password'] ?? '',
                    'profile' => $sec['profile'] ?? '',
                ];
            }
        }

        return response()->json(['status' => 'success', 'data' => $users]);
    }

    public function getRouterProfiles($routerId)
    {
        $router = Router::findOrFail($routerId);
        $mikrotikService = app(MikroTikService::class);
        $profiles = $mikrotikService->getPppoeProfiles($router);
        return response()->json(['status' => 'success', 'data' => $profiles]);
    }

    public function getRadiusGroups()
    {
        $groups = [];
        $server = Server::where('type', 'freeradius')->where('is_active', true)->first();
        if ($server) {
            $radiusService = app(RadiusService::class);
            // Assuming RadiusService has getGroups or we can query RadGroupReply
            if (method_exists($radiusService, 'getGroups')) {
                $groups = $radiusService->getGroups();
            } else {
                $groups = \App\Models\Radius\RadGroupReply::select('groupname')->distinct()->pluck('groupname');
            }
        }
        return response()->json(['status' => 'success', 'data' => $groups]);
    }

    public function destroy(Request $request, Customer $customer)
    {
        $removeFromServer = $request->boolean('remove_from_server');
        $this->deleteCustomerProcess($customer, $removeFromServer);

        return redirect()->route('cust.customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function batchDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customers,id',
        ]);

        $removeFromServer = $request->boolean('remove_from_server');
        $customers = Customer::whereIn('id', $request->ids)->get();

        foreach ($customers as $customer) {
            $this->deleteCustomerProcess($customer, $removeFromServer);
        }

        return redirect()->route('cust.customers.index')->with('success', count($customers) . ' Pelanggan berhasil dihapus.');
    }

    public function trashed(Request $request)
    {
        $query = Customer::onlyTrashed()->with(['package', 'area', 'odp', 'router', 'server', 'sales'])->latest('deleted_at');

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(username) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('sales_id')) {
            $query->where('sales_id', $request->sales_id);
        }

        $perPage = 15;
        if ($request->filled('per_page')) {
            if ($request->per_page === 'all') {
                $perPage = $query->count() > 0 ? $query->count() : 1;
            } else {
                $perPage = (int) $request->per_page;
            }
        }

        $customers = $query->paginate($perPage)->withQueryString();
        $filters = $request->only(['search', 'area_id', 'sales_id', 'per_page']);

        $areas = Area::orderBy('name')->get();
        $users = User::where('is_active', true)->whereHas('customers')->orderBy('name')->get();

        return Inertia::render('Cust/Customers/Trashed', compact('customers', 'areas', 'users', 'filters'));
    }

    public function restore($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();
        return redirect()->route('cust.customers.trashed')->with('success', 'Pelanggan berhasil direstore.');
    }

    public function forceDelete($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        
        // Any other related manual cascade deletes here if needed.
        // CustomerCoordinate is cascade deleted? Let's assume it is or we delete it.
        CustomerCoordinate::where('customer_id', $customer->id)->delete();
        
        $customer->forceDelete();
        return redirect()->route('cust.customers.trashed')->with('success', 'Pelanggan berhasil dihapus permanen.');
    }

    public function batchRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customers,id',
        ]);

        Customer::onlyTrashed()->whereIn('id', $request->ids)->restore();

        return redirect()->route('cust.customers.trashed')->with('success', count($request->ids) . ' Pelanggan berhasil direstore.');
    }

    public function batchForceDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customers,id',
        ]);

        $customers = Customer::onlyTrashed()->whereIn('id', $request->ids)->get();
        foreach ($customers as $customer) {
            CustomerCoordinate::where('customer_id', $customer->id)->delete();
            $customer->forceDelete();
        }

        return redirect()->route('cust.customers.trashed')->with('success', count($request->ids) . ' Pelanggan berhasil dihapus permanen.');
    }

    private function deleteCustomerProcess(Customer $customer, bool $removeFromServer)
    {
        if ($removeFromServer && $customer->username) {
            try {
                if ($customer->server_id) {
                    $server = Server::find($customer->server_id);
                    if ($server && $server->type === 'freeradius') {
                        $radiusService = app(RadiusService::class);
                        $radiusService->connectTo($server);
                        $radiusService->deleteUser($customer->username);
                    }
                } elseif ($customer->router_id) {
                    $router = Router::find($customer->router_id);
                    if ($router) {
                        $mikrotikService = app(MikroTikService::class);
                        $mikrotikService->removeSecret($router, $customer->username);
                    }
                }
            } catch (\Throwable $e) {
                \Log::error("Failed to remove user from server: " . $e->getMessage());
            }
        }

        if ($customer->sync_db_pusat) {
            app(\App\Services\RadiusApiService::class)->deleteCustomer($customer);
        }

        $customer->delete();
    }
}
