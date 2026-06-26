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
        if ($request->has('registered_month')) {
            $query->whereMonth('registration_date', $request->get('registered_month'));
        }
        if ($request->has('registered_year')) {
            $query->whereYear('registration_date', $request->get('registered_year'));
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

        // Belum Bayar (gabungan Jatuh Tempo + Deadline dari web)
        // Yaitu: tagihan bulan ini belum dibayar, BUKAN pelanggan telat, BUKAN pelanggan baru
        $belumBayarBalances = \App\Models\MonthlyBalance::with('customer.package')
            ->where('period', $periodString)
            ->where('status', '!=', 'paid')
            ->whereNotIn('customer_id', $telatCustomersIds)
            ->whereNotIn('customer_id', $baruCustomersIds)
            ->get();
            
        $belumBayar = $belumBayarBalances->count();
        $rpBelumBayar = $belumBayarBalances->sum(function ($mb) {
            return $mb->balance > 0 ? $mb->balance : ($mb->customer ? $mb->customer->getEffectivePrice() : 0);
        });
        
        $telatBayarBalances = \App\Models\MonthlyBalance::with('customer.package')
            ->where('period', '<', $periodString)
            ->where('status', '!=', 'paid')
            ->get();
            
        $rpTelatBayar = $telatBayarBalances->sum(function ($mb) {
            return $mb->balance > 0 ? $mb->balance : ($mb->customer ? $mb->customer->getEffectivePrice() : 0);
        });
        $telatBayar = count($telatCustomersIds);

        // Lunas Bulan Ini (lunas bulan ini, BUKAN pelanggan telat, BUKAN pelanggan baru)
        $lunasBulanIniQuery = \App\Models\MonthlyBalance::where('period', $periodString)
            ->where('status', 'paid')
            ->whereNotIn('customer_id', $telatCustomersIds)
            ->whereNotIn('customer_id', $baruCustomersIds);
            
        $lunasBulanIni = $lunasBulanIniQuery->count();
        $rpLunasBulanIni = $lunasBulanIniQuery->sum('paid_amount');

        // Transaksi Bulan Ini (jumlah payment yang dilakukan pada bulan tersebut)
        $transaksiQuery = \App\Models\Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled');
            
        $transaksiBulanIni = $transaksiQuery->count();
        $rpTransaksiBulanIni = $transaksiQuery->sum('paid_amount');

        // Billing Projections
        $billingProjections = Customer::where('status', 'active')
            ->where('is_on_leave', false)
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->groupBy('billing_date')
            ->selectRaw('billing_date, SUM(packages.price) as total_price')
            ->pluck('total_price', 'billing_date')
            ->toArray();

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
                'billing_projections' => $billingProjections
            ]
        ]);
    }

    /**
     * GET /api/v1/customers/dashboard-search
     */
    public function dashboardSearch(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $billingStart = $request->input('billing_start');
        $billingEnd = $request->input('billing_end');

        // If no search and no billing date is selected, return empty
        if ((!$search || strlen($search) < 3) && $billingStart === null && $billingEnd === null) {
            return response()->json(['status' => 'success', 'data' => []]);
        }

        $query = Customer::with(['monthlyBalances', 'package'])
            ->where('status', 'active')
            ->where('is_on_leave', false);

        if ($search && strlen($search) >= 3) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('username', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%")
                    ->orWhere('address', 'ilike', "%{$search}%");
            });
        }

        if ($billingStart !== null) $query->where('billing_date', '>=', $billingStart);
        if ($billingEnd !== null) $query->where('billing_date', '<=', $billingEnd);

        // Limit results to prevent massive payloads if they just select 1 date (though 1 date is usually small)
        $customers = $query->limit(50)->get();

        $currentPeriod = now()->format('Y-m');
        $currentDay = now()->day;
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $results = $customers->map(function ($customer) use ($currentPeriod, $currentDay, $currentMonth, $currentYear) {
            $statusStr = 'Unknown';
            $isBaru = ($customer->registration_date && $customer->registration_date->month === $currentMonth) && ($customer->registration_date->year === $currentYear);
            
            $telatBalances = $customer->monthlyBalances->where('period', '<', $currentPeriod)->where('status', '!=', 'paid');
            $isTelat = $telatBalances->isNotEmpty();
            $currentBalance = $customer->monthlyBalances->where('period', $currentPeriod)->first();
            
            if ($isBaru) {
                $statusStr = 'Baru';
            } else if ($isTelat) {
                $statusStr = 'Telat Bayar';
            } else if ($currentBalance && $currentBalance->status === 'paid') {
                $statusStr = 'Lunas';
            } else {
                if ($customer->billing_date < $currentDay) {
                    $statusStr = 'Jatuh Tempo';
                } else {
                    $statusStr = 'Deadline';
                }
            }
            
            // Total unpaid is sum of balance for all unpaid bills
            $totalUnpaid = $customer->monthlyBalances->where('status', '!=', 'paid')->sum(function ($mb) use ($customer) {
                return $mb->balance > 0 ? $mb->balance : $customer->getEffectivePrice();
            });
            
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'payment_status' => $statusStr,
                'total_unpaid' => $totalUnpaid,
                'phone' => $customer->phone,
                'is_isolated' => (bool) $customer->is_isolated,
                'is_on_leave' => (bool) $customer->is_on_leave,
                'billing_date' => $customer->billing_date
            ];
        });

        $user = $request->user();
        $capabilities = [
            'view_customers' => $user ? $user->hasCapability('billing.customers.view') : false,
            'edit_customers' => $user ? $user->hasCapability('billing.customers.edit') : false,
            'create_payments' => $user ? $user->hasCapability('billing.payments.create') : false,
            'send_wa' => $user ? $user->hasCapability('operational.whatsapp.send') : false,
        ];

        return response()->json([
            'status' => 'success',
            'capabilities' => $capabilities,
            'data' => $results
        ]);
    }

    /**
     * GET /api/v1/customers/dashboard-belum-bayar
     */
    public function dashboardBelumBayar(Request $request): JsonResponse
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $periodString = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT);

        $telatCustomersIds = \App\Models\MonthlyBalance::whereHas('customer')
            ->where('period', '<', $periodString)
            ->where('status', '!=', 'paid')
            ->pluck('customer_id')
            ->unique()
            ->toArray();

        $baruCustomersIds = Customer::whereMonth('registration_date', $month)
            ->whereYear('registration_date', $year)
            ->pluck('id')
            ->toArray();

        $belumBayarCustomerIds = \App\Models\MonthlyBalance::where('period', $periodString)
            ->where('status', '!=', 'paid')
            ->whereNotIn('customer_id', $telatCustomersIds)
            ->whereNotIn('customer_id', $baruCustomersIds)
            ->pluck('customer_id')
            ->toArray();

        $customers = Customer::with(['monthlyBalances', 'package'])
            ->whereIn('id', $belumBayarCustomerIds)
            ->get();

        $currentPeriod = now()->format('Y-m');
        $currentDay = now()->day;

        $formatted = $customers->map(function ($customer) use ($currentPeriod, $currentDay) {
            $totalUnpaid = $customer->monthlyBalances->where('status', '!=', 'paid')->sum(function ($mb) use ($customer) {
                return $mb->balance > 0 ? $mb->balance : $customer->getEffectivePrice();
            });
            
            $statusStr = 'Belum Bayar';
            if ($customer->billing_date < $currentDay) {
                $statusStr = 'Jatuh Tempo';
            } elseif ($customer->billing_date == $currentDay) {
                $statusStr = 'Deadline';
            }

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'payment_status' => $statusStr,
                'total_unpaid' => $totalUnpaid,
                'phone' => $customer->phone,
                'is_isolated' => (bool) $customer->is_isolated,
                'is_on_leave' => (bool) $customer->is_on_leave,
                'billing_date' => $customer->billing_date
            ];
        });

        $user = $request->user();
        $capabilities = [
            'view_customers' => $user ? $user->hasCapability('billing.customers.view') : false,
            'edit_customers' => $user ? $user->hasCapability('billing.customers.edit') : false,
            'create_payments' => $user ? $user->hasCapability('billing.payments.create') : false,
            'send_wa' => $user ? $user->hasCapability('operational.whatsapp.send') : false,
        ];

        return response()->json([
            'status' => 'success',
            'capabilities' => $capabilities,
            'data' => $formatted
        ]);
    }

    /**
     * GET /api/v1/customers/dashboard-telat-bayar
     */
    public function dashboardTelatBayar(Request $request): JsonResponse
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $periodString = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT);

        $telatCustomersIds = \App\Models\MonthlyBalance::whereHas('customer')
            ->where('period', '<', $periodString)
            ->where('status', '!=', 'paid')
            ->pluck('customer_id')
            ->unique()
            ->toArray();

        $customers = Customer::with(['monthlyBalances', 'package'])
            ->whereIn('id', $telatCustomersIds)
            ->get();

        $currentPeriod = now()->format('Y-m');
        $currentDay = now()->day;

        $formatted = $customers->map(function ($customer) use ($currentPeriod, $currentDay) {
            $totalUnpaid = $customer->monthlyBalances->where('status', '!=', 'paid')->sum(function ($mb) use ($customer) {
                return $mb->balance > 0 ? $mb->balance : $customer->getEffectivePrice();
            });
            
            $statusStr = 'Telat Bayar';

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'payment_status' => $statusStr,
                'total_unpaid' => $totalUnpaid,
                'phone' => $customer->phone,
                'is_isolated' => (bool) $customer->is_isolated,
                'is_on_leave' => (bool) $customer->is_on_leave,
                'billing_date' => $customer->billing_date
            ];
        });

        $user = $request->user();
        $capabilities = [
            'view_customers' => $user ? $user->hasCapability('billing.customers.view') : false,
            'edit_customers' => $user ? $user->hasCapability('billing.customers.edit') : false,
            'create_payments' => $user ? $user->hasCapability('billing.payments.create') : false,
            'send_wa' => $user ? $user->hasCapability('operational.whatsapp.send') : false,
        ];

        return response()->json([
            'status' => 'success',
            'capabilities' => $capabilities,
            'data' => $formatted
        ]);
    }

    /**
     * GET /api/v1/customers/dashboard-lunas-bulan-ini
     */
    public function dashboardLunasBulanIni(Request $request): JsonResponse
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $periodString = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT);

        $telatCustomersIds = \App\Models\MonthlyBalance::whereHas('customer')
            ->where('period', '<', $periodString)
            ->where('status', '!=', 'paid')
            ->pluck('customer_id')
            ->unique()
            ->toArray();

        $baruCustomersIds = Customer::whereMonth('registration_date', $month)
            ->whereYear('registration_date', $year)
            ->pluck('id')
            ->toArray();

        $lunasCustomerIds = \App\Models\MonthlyBalance::where('period', $periodString)
            ->where('status', 'paid')
            ->whereNotIn('customer_id', $telatCustomersIds)
            ->whereNotIn('customer_id', $baruCustomersIds)
            ->pluck('customer_id')
            ->toArray();

        $customers = Customer::with(['monthlyBalances', 'package', 'invoices'])
            ->whereIn('id', $lunasCustomerIds)
            ->get();

        $currentPeriod = now()->format('Y-m');
        $currentDay = now()->day;

        $formatted = $customers->map(function ($customer) use ($currentPeriod, $currentDay) {
            $statusStr = 'Lunas';
            $latestInvoice = $customer->invoices->sortByDesc('created_at')->first();

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'payment_status' => $statusStr,
                'total_unpaid' => 0,
                'phone' => $customer->phone,
                'is_isolated' => (bool) $customer->is_isolated,
                'is_on_leave' => (bool) $customer->is_on_leave,
                'billing_date' => $customer->billing_date,
                'latest_invoice_id' => $latestInvoice ? $latestInvoice->uuid : null
            ];
        });

        $user = $request->user();
        $capabilities = [
            'view_customers' => $user ? $user->hasCapability('billing.customers.view') : false,
            'edit_customers' => $user ? $user->hasCapability('billing.customers.edit') : false,
            'create_payments' => $user ? $user->hasCapability('billing.payments.create') : false,
            'send_wa' => $user ? $user->hasCapability('operational.whatsapp.send') : false,
        ];

        return response()->json([
            'status' => 'success',
            'capabilities' => $capabilities,
            'data' => $formatted
        ]);
    }

    /**
     * GET /api/v1/customers/dashboard-transaksi-bulan-ini
     */
    public function dashboardTransaksiBulanIni(Request $request): JsonResponse
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $startDate = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01 00:00:00";
        $endDate = date("Y-m-t 23:59:59", strtotime($startDate));

        $transaksiCustomerIds = \App\Models\Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->pluck('customer_id')
            ->unique()
            ->toArray();

        $customers = Customer::with(['monthlyBalances', 'package', 'invoices'])
            ->whereIn('id', $transaksiCustomerIds)
            ->get();

        $currentPeriod = now()->format('Y-m');
        $currentDay = now()->day;

        $formatted = $customers->map(function ($customer) use ($currentPeriod, $currentDay) {
            $totalUnpaid = $customer->monthlyBalances->where('status', '!=', 'paid')->sum('balance');
            $statusStr = 'Transaksi Bulan Ini';
            $latestInvoice = $customer->invoices->sortByDesc('created_at')->first();

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'payment_status' => $statusStr,
                'total_unpaid' => $totalUnpaid,
                'phone' => $customer->phone,
                'is_isolated' => (bool) $customer->is_isolated,
                'is_on_leave' => (bool) $customer->is_on_leave,
                'billing_date' => $customer->billing_date,
                'latest_invoice_id' => $latestInvoice ? $latestInvoice->uuid : null
            ];
        });

        $user = $request->user();
        $capabilities = [
            'view_customers' => $user ? $user->hasCapability('billing.customers.view') : false,
            'edit_customers' => $user ? $user->hasCapability('billing.customers.edit') : false,
            'create_payments' => $user ? $user->hasCapability('billing.payments.create') : false,
            'send_wa' => $user ? $user->hasCapability('operational.whatsapp.send') : false,
        ];

        return response()->json([
            'status' => 'success',
            'capabilities' => $capabilities,
            'data' => $formatted
        ]);
    }

    /**
     * GET /api/v1/customers/{customer}/payment-options
     */
    public function paymentOptions(\App\Models\Customer $customer): JsonResponse
    {
        $service = new \App\Services\PaymentService();
        $options = $service->getPaymentMonths($customer);
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'months' => $options
            ]
        ]);
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
        return response()->json(['status' => 'success', 'message' => "{$customer->name} berhasil diisolir."]);
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

            if ($customer->router_id && $customer->router) {
                $mikrotik->kickActiveSession($customer->router, $customer->username);
            }
        }

        if ($customer->username) {
            $acs->rebootDevice($customer->username);
        }

        $customer->update(['is_isolated' => false, 'isolated_since' => null]);
        return response()->json(['status' => 'success', 'message' => "{$customer->name} berhasil dilepas dari isolir."]);
    }

    public function setLeave(Request $request, Customer $customer, \App\Services\MikroTik\MikroTikService $mikrotik)
    {
        if ($customer->router_id && $customer->username) {
            $mikrotik->removeSecret($customer->router, $customer->username);
        }

        $customer->update([
            'is_on_leave' => true,
            'status' => 'inactive',
            'leave_start' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => "{$customer->name} berhasil dinonaktifkan (Cuti)."]);
    }

    public function removeLeave(Request $request, Customer $customer)
    {
        $customer->update([
            'is_on_leave' => false,
            'status' => 'active',
            'leave_start' => null,
            'leave_end' => null,
        ]);

        return response()->json(['status' => 'success', 'message' => "{$customer->name} berhasil dikembalikan ke Daftar Pelanggan."]);
    }
}
