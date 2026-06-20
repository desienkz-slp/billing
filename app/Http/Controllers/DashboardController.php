<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Area;
use App\Models\Package;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private PermissionService $permissionService
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        // Dashboard Metrics Calculation
        $currentPeriod = now()->format('Y-m');
        $currentDay = now()->day;

        $telatCustomersIds = \App\Models\MonthlyBalance::where('period', '<', $currentPeriod)
            ->where('status', '!=', 'paid')
            ->pluck('customer_id')
            ->unique()
            ->values()
            ->all();

        $baruCustomersIds = Customer::whereMonth('registration_date', now()->month)
            ->whereYear('registration_date', now()->year)
            ->pluck('id')
            ->toArray();

        $stats = [
            'total_customers' => Customer::count(),
            'baru' => count($baruCustomersIds),
            'lunas' => \App\Models\MonthlyBalance::where('period', $currentPeriod)
                ->where('status', 'paid')
                ->whereNotIn('customer_id', $telatCustomersIds)
                ->whereNotIn('customer_id', $baruCustomersIds)
                ->count(),
            'telat' => count($telatCustomersIds),
            'jatuh_tempo' => \App\Models\MonthlyBalance::where('period', $currentPeriod)
                ->where('status', '!=', 'paid')
                ->whereNotIn('customer_id', $telatCustomersIds)
                ->whereNotIn('customer_id', $baruCustomersIds)
                ->whereHas('customer', function($q) use ($currentDay) {
                    $q->where('billing_date', '<', $currentDay);
                })->count(),
            'deadline' => \App\Models\MonthlyBalance::where('period', $currentPeriod)
                ->where('status', '!=', 'paid')
                ->whereNotIn('customer_id', $telatCustomersIds)
                ->whereNotIn('customer_id', $baruCustomersIds)
                ->whereHas('customer', function($q) use ($currentDay) {
                    $q->where('billing_date', '>=', $currentDay);
                })->count(),
        ];

        // Hitung Wajib Setor (total uang tunai yang diterima hari ini dikurangi fee)
        $wajibSetor = Payment::where('collected_by', $user->id)
            ->whereDate('payment_date', now()->toDateString())
            ->where('status', 'paid')
            ->sum(DB::raw('paid_amount - COALESCE(collector_fee_amount, 0)'));
            
        $stats['wajib_setor'] = $wajibSetor;

        // Customers Data for Table
        $query = Customer::with([
            'package', 
            'area', 
            'odp', 
            'sales', 
            'monthlyBalances' => function($q) use ($currentPeriod) {
                $q->where('period', '<=', $currentPeriod);
            }
        ])->where('status', 'active')->where('is_on_leave', false);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('username', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%")
                  ->orWhere('address', 'ilike', "%{$search}%");
            });
        }
        if ($areaId = $request->get('area_id')) $query->where('area_id', $areaId);
        if ($salesId = $request->get('sales_id')) $query->where('sales_id', $salesId);
        if ($packageId = $request->get('package_id')) $query->where('package_id', $packageId);
        if ($request->filled('status')) $query->where('status', $request->get('status'));
        if ($request->filled('is_isolated')) $query->where('is_isolated', $request->boolean('is_isolated'));

        // KPI Filter logic
        if ($kpiFilter = $request->get('kpi_filter')) {
            if ($kpiFilter === 'baru') {
                $query->whereIn('id', $baruCustomersIds);
            } else if ($kpiFilter === 'lunas') {
                $query->whereHas('monthlyBalances', function($q) use ($currentPeriod) {
                    $q->where('period', $currentPeriod)->where('status', 'paid');
                })->whereNotIn('id', $telatCustomersIds)
                  ->whereNotIn('id', $baruCustomersIds);
            } else if ($kpiFilter === 'telat') {
                $query->whereIn('id', $telatCustomersIds);
            } else if ($kpiFilter === 'jatuh_tempo') {
                $query->whereHas('monthlyBalances', function($q) use ($currentPeriod) {
                    $q->where('period', $currentPeriod)->where('status', '!=', 'paid');
                })->whereNotIn('id', $telatCustomersIds)
                  ->whereNotIn('id', $baruCustomersIds)
                  ->where('billing_date', '<', $currentDay);
            } else if ($kpiFilter === 'deadline') {
                $query->whereHas('monthlyBalances', function($q) use ($currentPeriod) {
                    $q->where('period', $currentPeriod)->where('status', '!=', 'paid');
                })->whereNotIn('id', $telatCustomersIds)
                  ->whereNotIn('id', $baruCustomersIds)
                  ->where('billing_date', '>=', $currentDay);
            }
            \Log::info("KPI FILTER APPLIED: {$kpiFilter} | Result Count: " . $query->count());
        } else {
            \Log::info("NO KPI FILTER APPLIED.");
        }

        $sortBy = $request->get('sort', 'name');
        $sortDir = $request->get('dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = 10;
        if ($request->filled('per_page')) {
            if ($request->per_page === 'all') {
                $perPage = $query->count() > 0 ? $query->count() : 1;
            } else {
                $perPage = (int) $request->per_page;
            }
        }
        
        $filters = $request->only(['search', 'area_id', 'sales_id', 'per_page', 'kpi_filter']);

        $customers = $query->paginate($perPage)->withQueryString();
        
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $customers->getCollection()->transform(function ($customer) use ($currentPeriod, $currentDay, $currentMonth, $currentYear) {
            $statusStr = 'Unknown';
            $isBaru = ($customer->registration_date->month === $currentMonth) && ($customer->registration_date->year === $currentYear);
            $isTelat = $customer->monthlyBalances->where('period', '<', $currentPeriod)->where('status', '!=', 'paid')->isNotEmpty();
            $currentBalance = $customer->monthlyBalances->where('period', $currentPeriod)->first();
            
            if ($isBaru) {
                $statusStr = 'Baru';
            } else if ($isTelat) {
                $statusStr = 'Telat';
            } else if ($currentBalance && $currentBalance->status === 'paid') {
                $statusStr = 'Lunas';
            } else {
                if ($customer->billing_date < $currentDay) {
                    $statusStr = 'Jatuh Tempo';
                } else {
                    $statusStr = 'Deadline';
                }
            }
            
            $customer->payment_status = $statusStr;
            return $customer;
        });

        $areas = Area::orderBy('name')->get();
        $packages = Package::orderBy('name')->get();
        $users = User::where('is_active', true)->whereHas('customers')->orderBy('name')->get();

        return Inertia::render('Cust/Dashboard', compact(
            'capabilities', 'stats',
            'customers', 'areas', 'packages', 'users', 'filters'
        ));
    }
}
