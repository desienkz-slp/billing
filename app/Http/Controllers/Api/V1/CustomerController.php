<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * GET /api/v1/customers
     */
    public function index(Request $request)
    {
        $query = Customer::with(['package', 'area', 'router']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('username', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%")
                  ->orWhere('address', 'ilike', "%{$search}%")
                  ->orWhere('customer_id_display', 'ilike', "%{$search}%");
            });
        }

        if ($areaId = $request->get('area_id')) {
            $query->where('area_id', $areaId);
        }
        if ($packageId = $request->get('package_id')) {
            $query->where('package_id', $packageId);
        }
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->has('is_isolated')) {
            $query->where('is_isolated', $request->boolean('is_isolated'));
        }

        $sortBy = $request->get('sort_by', 'name');
        $sortDir = $request->get('sort_dir', 'asc');
        $allowed = ['name', 'username', 'registration_date', 'created_at', 'billing_date'];
        if (in_array($sortBy, $allowed)) {
            $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
        }

        $perPage = min((int) $request->get('per_page', 25), 100);
        return CustomerResource::collection($query->paginate($perPage));
    }

    /**
     * POST /api/v1/customers
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['uuid'] = (string) Str::uuid();
        $data['status'] = 'active';
        $data['is_isolated'] = false;
        $data['registration_date'] = $data['registration_date'] ?? now()->toDateString();

        $customer = Customer::create($data);
        $customer->load(['package', 'area', 'router']);

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/v1/customers/{customer}
     */
    public function show(Customer $customer): CustomerResource
    {
        $customer->load(['package', 'area', 'router', 'server', 'odp', 'sales']);
        return new CustomerResource($customer);
    }

    /**
     * PUT /api/v1/customers/{customer}
     */
    public function update(StoreCustomerRequest $request, Customer $customer): CustomerResource
    {
        $customer->update($request->validated());
        $customer->load(['package', 'area', 'router']);
        return new CustomerResource($customer);
    }

    /**
     * DELETE /api/v1/customers/{customer}
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();
        return response()->json(['message' => 'Pelanggan berhasil dihapus.']);
    }

    /**
     * GET /api/v1/customers/stats
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'data' => [
                'total' => Customer::count(),
                'active' => Customer::where('status', 'active')->count(),
                'isolated' => Customer::where('is_isolated', true)->count(),
                'inactive' => Customer::where('status', '!=', 'active')->count(),
                'new_this_month' => Customer::whereMonth('registration_date', now()->month)
                    ->whereYear('registration_date', now()->year)
                    ->count(),
            ],
        ]);
    }
}
