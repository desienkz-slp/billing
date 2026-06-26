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

    /**
     * GET /api/v1/customers/dashboard-stats
     */
    public function dashboardStats(Request $request): JsonResponse
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $periodString = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT);
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Total Pelanggan (semua)
        $totalPelanggan = Customer::count();

        // 1. Dapatkan IDs untuk "Telat" (punya tagihan bulan-bulan sebelumnya yang belum dibayar)
        $telatCustomersIds = \App\Models\MonthlyBalance::whereHas('customer')
            ->where('period', '<', $periodString)
            ->where('status', '!=', 'paid')
            ->pluck('customer_id')
            ->unique()
            ->toArray();

        // 2. Dapatkan IDs untuk "Baru" (daftar di bulan yang difilter)
        $baruCustomersIds = Customer::whereMonth('registration_date', $month)
            ->whereYear('registration_date', $year)
            ->pluck('id')
            ->toArray();

        $pelangganBaru = count($baruCustomersIds);
        $telatBayar = count($telatCustomersIds);

        $rpPelanggan = Customer::where('customers.status', 'active')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->sum('packages.price');

        $billingStart = $request->input('billing_start');
        $billingEnd = $request->input('billing_end');
        
        $customerIdsFiltered = null;
        if ($billingStart !== null || $billingEnd !== null) {
            $customerQuery = Customer::query();
            if ($billingStart !== null) $customerQuery->where('billing_date', '>=', $billingStart);
            if ($billingEnd !== null) $customerQuery->where('billing_date', '<=', $billingEnd);
            $customerIdsFiltered = $customerQuery->pluck('id')->toArray();
        }

        // Belum Bayar (gabungan Jatuh Tempo + Deadline dari web)
        // Yaitu: tagihan bulan ini belum dibayar, BUKAN pelanggan telat, BUKAN pelanggan baru
        $belumBayarQuery = \App\Models\MonthlyBalance::where('period', $periodString)
            ->where('status', '!=', 'paid')
            ->whereNotIn('customer_id', $telatCustomersIds)
            ->whereNotIn('customer_id', $baruCustomersIds);
            
        if ($customerIdsFiltered !== null) $belumBayarQuery->whereIn('customer_id', $customerIdsFiltered);
            
        $belumBayar = $belumBayarQuery->count();
        $rpBelumBayar = $belumBayarQuery->sum('balance');
        
        $telatBayarQuery = \App\Models\MonthlyBalance::where('period', '<', $periodString)
            ->where('status', '!=', 'paid');
            
        if ($customerIdsFiltered !== null) $telatBayarQuery->whereIn('customer_id', $customerIdsFiltered);
            
        $rpTelatBayar = $telatBayarQuery->sum('balance');
        
        // Count for telat_bayar needs to apply filter to the distinct customers
        if ($customerIdsFiltered !== null) {
            $telatBayar = collect($telatCustomersIds)->intersect($customerIdsFiltered)->count();
        } else {
            $telatBayar = count($telatCustomersIds);
        }

        // Lunas Bulan Ini (lunas bulan ini, BUKAN pelanggan telat, BUKAN pelanggan baru)
        $lunasBulanIniQuery = \App\Models\MonthlyBalance::where('period', $periodString)
            ->where('status', 'paid')
            ->whereNotIn('customer_id', $telatCustomersIds)
            ->whereNotIn('customer_id', $baruCustomersIds);
            
        if ($customerIdsFiltered !== null) $lunasBulanIniQuery->whereIn('customer_id', $customerIdsFiltered);
            
        $lunasBulanIni = $lunasBulanIniQuery->count();
        $rpLunasBulanIni = $lunasBulanIniQuery->sum('paid_amount');

        // Transaksi Bulan Ini (jumlah payment yang dilakukan pada bulan tersebut)
        $transaksiQuery = \App\Models\Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled');
            
        if ($customerIdsFiltered !== null) $transaksiQuery->whereIn('customer_id', $customerIdsFiltered);
            
        $transaksiBulanIni = $transaksiQuery->count();
        $rpTransaksiBulanIni = $transaksiQuery->sum('paid_amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_pelanggan' => $totalPelanggan,
                'rp_pelanggan' => (int) $rpPelanggan,
                'pelanggan_baru' => $pelangganBaru,
                'belum_bayar' => $belumBayar,
                'rp_belum_bayar' => (int) $rpBelumBayar,
                'telat_bayar' => $telatBayar,
                'rp_telat_bayar' => (int) $rpTelatBayar,
                'lunas_bulan_ini' => $lunasBulanIni,
                'rp_lunas_bulan_ini' => (int) $rpLunasBulanIni,
                'transaksi_bulan_ini' => $transaksiBulanIni,
                'rp_transaksi_bulan_ini' => (int) $rpTransaksiBulanIni,
            ]
        ]);
    }
}
