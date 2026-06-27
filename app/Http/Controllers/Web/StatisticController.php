<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\HiddenMonth;
use App\Models\MonthlyBalance;
use App\Models\Expense;
use App\Models\FeeWithdrawal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = session('tenant_id', $request->user()->tenant_id);
        if (!$tenantId) {
            abort(403, 'No active tenant.');
        }

        $bulan = $request->input('bulan', date('Y-m'));

        return inertia('Reports/Statistics', ['bulan' => $bulan]);
    }

    public function toggleHideMonth(Request $request)
    {
        $tenantId = session('tenant_id', $request->user()->tenant_id);
        $periode = $request->input('periode');
        if (!preg_match('/^\d{4}-\d{2}$/', $periode)) {
            return response()->json(['status' => 'error', 'message' => 'Format periode tidak valid (YYYY-MM).']);
        }

        $hidden = HiddenMonth::where('tenant_id', $tenantId)->where('periode', $periode)->first();
        if ($hidden) {
            $hidden->delete();
            return response()->json(['status' => 'success', 'action' => 'shown', 'message' => 'Periode '.$periode.' ditampilkan kembali.']);
        } else {
            HiddenMonth::create(['tenant_id' => $tenantId, 'periode' => $periode]);
            return response()->json(['status' => 'success', 'action' => 'hidden', 'message' => 'Periode '.$periode.' disembunyikan.']);
        }
    }

    public function apiData(Request $request)
    {
        $tenantId = session('tenant_id', $request->user()->tenant_id);
        $action = $request->input('action');
        $bulan = $request->input('bulan', date('Y-m'));
        [$tahun, $mn] = explode('-', $bulan);
        
        $mStart = Carbon::parse($bulan . '-01')->startOfMonth();
        $mEnd = Carbon::parse($bulan . '-01')->endOfMonth();
        $pmStart = Carbon::parse($bulan . '-01')->subMonth()->startOfMonth();
        $pmEnd = Carbon::parse($bulan . '-01')->subMonth()->endOfMonth();
        $yrStart = Carbon::parse($tahun . '-01-01')->startOfYear();
        $yrEnd = Carbon::parse($tahun . '-01-01')->endOfYear();
        $today = Carbon::today();

        switch ($action) {
            case 'list_hidden_months':
                $hidden = HiddenMonth::where('tenant_id', $tenantId)->pluck('periode');
                return response()->json(['status' => 'success', 'data' => $hidden]);

            case 'payment_summary':
                $payToday = Payment::where('tenant_id', $tenantId)->whereDate('payment_date', $today)->sum('amount')
                          + \App\Models\OtherIncome::where('tenant_id', $tenantId)->whereDate('income_date', $today)->sum('amount');
                
                $payMonthQ = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$mStart, $mEnd]);
                $payMonth = $payMonthQ->sum('amount') 
                          + \App\Models\OtherIncome::where('tenant_id', $tenantId)->whereBetween('income_date', [$mStart, $mEnd])->sum('amount');
                $txnMonth = $payMonthQ->count();
                
                $payLastMonth = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$pmStart, $pmEnd])->sum('amount')
                              + \App\Models\OtherIncome::where('tenant_id', $tenantId)->whereBetween('income_date', [$pmStart, $pmEnd])->sum('amount');
                
                $payYearly = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$yrStart, $yrEnd])->sum('amount')
                           + \App\Models\OtherIncome::where('tenant_id', $tenantId)->whereBetween('income_date', [$yrStart, $yrEnd])->sum('amount');
                
                $unpaidBalance = MonthlyBalance::where('tenant_id', $tenantId)->where('balance', '>', 0);
                $unpaidCount = $unpaidBalance->distinct('customer_id')->count('customer_id');
                $unpaidAmount = $unpaidBalance->sum('balance');
                
                $overdueCnt = MonthlyBalance::where('tenant_id', $tenantId)->where('status', '!=', 'paid')->where('period', '<', $bulan)->count();
                
                $activeCust = max(1, Customer::where('tenant_id', $tenantId)->where('status', 'active')->where('is_isolated', false)->count());
                $paidCust = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$mStart, $mEnd])->distinct('customer_id')->count('customer_id');
                
                $successRate = round(($paidCust / $activeCust) * 100, 1);
                $arpu = round($payMonth / $activeCust, 0);
                $growth = $payLastMonth > 0 ? round(($payMonth - $payLastMonth) / $payLastMonth * 100, 1) : ($payMonth > 0 ? 100 : 0);
                
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'pay_today'      => (float)$payToday,
                        'pay_month'      => (float)$payMonth,
                        'pay_yearly'     => (float)$payYearly,
                        'txn_month'      => (int)$txnMonth,
                        'unpaid_count'   => (int)$unpaidCount,
                        'unpaid_amount'  => (float)$unpaidAmount,
                        'overdue_count'  => $overdueCnt,
                        'success_rate'   => $successRate,
                        'arpu'           => $arpu,
                        'growth'         => $growth,
                        'active_cust'    => $activeCust,
                    ]
                ]);

            case 'payment_daily':
                $startDaily = Carbon::today()->subDays(29)->startOfDay();
                $endDaily = Carbon::today()->endOfDay();
                $payments = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$startDaily, $endDaily])
                    ->selectRaw('DATE(payment_date) as tgl, SUM(amount) as total')->groupBy('tgl')->get()->keyBy('tgl');
                $others = \App\Models\OtherIncome::where('tenant_id', $tenantId)->whereBetween('income_date', [$startDaily, $endDaily])
                    ->selectRaw('DATE(income_date) as tgl, SUM(amount) as total')->groupBy('tgl')->get()->keyBy('tgl');
                
                $daily = [];
                for ($i = 29; $i >= 0; $i--) {
                    $d = Carbon::today()->subDays($i)->format('Y-m-d');
                    $daily[] = [
                        'tgl' => $d,
                        'total' => ($payments[$d]->total ?? 0) + ($others[$d]->total ?? 0)
                    ];
                }
                return response()->json(['status' => 'success', 'data' => $daily]);

            case 'payment_monthly':
                $startMonthly = Carbon::now()->subMonths(11)->startOfMonth();
                $payments = Payment::where('tenant_id', $tenantId)->where('payment_date', '>=', $startMonthly)
                    ->selectRaw('to_char(payment_date, \'YYYY-MM\') as bln, SUM(amount) as total')->groupBy('bln')->get()->keyBy('bln');
                $others = \App\Models\OtherIncome::where('tenant_id', $tenantId)->where('income_date', '>=', $startMonthly)
                    ->selectRaw('to_char(income_date, \'YYYY-MM\') as bln, SUM(amount) as total')->groupBy('bln')->get()->keyBy('bln');
                
                $monthly = [];
                for ($i = 0; $i < 12; $i++) {
                    $m = $startMonthly->copy()->addMonths($i)->format('Y-m');
                    $monthly[] = [
                        'bln' => $m,
                        'total' => ($payments[$m]->total ?? 0) + ($others[$m]->total ?? 0)
                    ];
                }
                return response()->json(['status' => 'success', 'data' => $monthly]);

            case 'payment_methods':
                $methods = Payment::where('tenant_id', $tenantId)
                    ->whereBetween('payment_date', [$mStart, $mEnd])
                    ->selectRaw('payment_method as metode, SUM(amount) as total')
                    ->groupBy('metode')
                    ->orderByDesc('total')
                    ->get();
                return response()->json(['status' => 'success', 'data' => $methods]);

            case 'payment_heatmap':
                $hm = Payment::where('tenant_id', $tenantId)
                    ->whereBetween('payment_date', [$mStart, $mEnd])
                    ->selectRaw('EXTRACT(ISODOW FROM payment_date) as dow, EXTRACT(HOUR FROM created_at) as hr, COUNT(id) as c')
                    ->groupBy('dow', 'hr')
                    ->get();
                return response()->json(['status' => 'success', 'data' => $hm]);

            case 'customer_summary':
                $active = Customer::where('tenant_id', $tenantId)->where('status', 'active')->where('is_isolated', false)->count();
                $new = Customer::where('tenant_id', $tenantId)->whereBetween('created_at', [$mStart, $mEnd])->count();
                $cuti = Customer::where('tenant_id', $tenantId)->where('status', 'inactive')->count();
                $suspend = Customer::where('tenant_id', $tenantId)->where('is_isolated', true)->count();
                
                $pmActive = Customer::where('tenant_id', $tenantId)->where('status', 'active')->where('is_isolated', false)->where('created_at', '<=', $pmEnd)->count();
                $growthRate = $pmActive > 0 ? round(($active - $pmActive) / $pmActive * 100, 1) : ($active > 0 ? 100 : 0);
                
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'active' => $active,
                        'new' => $new,
                        'cuti' => $cuti,
                        'suspend' => $suspend,
                        'pppoe' => $active, // Simulating PPPoE
                        'growth_rate' => $growthRate
                    ]
                ]);

            case 'customer_growth':
                $start = Carbon::now()->subMonths(11)->startOfMonth();
                $months = [];
                for ($i=0; $i<12; $i++) {
                    $m = $start->copy()->addMonths($i);
                    $period = $m->format('Y-m');
                    $active = Customer::where('tenant_id', $tenantId)->where('status', 'active')->where('is_isolated', false)->where('created_at', '<=', $m->copy()->endOfMonth())->count();
                    $new = Customer::where('tenant_id', $tenantId)->whereBetween('created_at', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->count();
                    $churn = Customer::where('tenant_id', $tenantId)->where('status', 'inactive')->whereBetween('updated_at', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->count();
                    $months[] = ['bulan' => $period, 'active' => $active, 'new' => $new, 'churn' => $churn];
                }
                return response()->json(['status' => 'success', 'data' => $months]);

            case 'customer_by_area':
                $areas = DB::table('customers')
                    ->leftJoin('areas', 'customers.area_id', '=', 'areas.id')
                    ->where('customers.tenant_id', $tenantId)
                    ->where('customers.status', 'active')
                    ->where('customers.is_isolated', false)
                    ->select(DB::raw('COALESCE(areas.name, \'Tanpa Area\') as area_name'), DB::raw('COUNT(customers.id) as c'))
                    ->groupBy('area_name')
                    ->orderByDesc('c')
                    ->limit(10)
                    ->get();
                return response()->json(['status' => 'success', 'data' => $areas]);

            case 'churn_summary':
                $activeStart = Customer::where('tenant_id', $tenantId)->where('status', 'active')->where('is_isolated', false)->where('created_at', '<=', $pmEnd)->count();
                $activeStart = max(1, $activeStart);
                $churnedThisMonth = Customer::where('tenant_id', $tenantId)->where('status', 'inactive')->whereBetween('updated_at', [$mStart, $mEnd])->count();
                $churnRate = round(($churnedThisMonth / $activeStart) * 100, 1);
                
                $suspendLong = Customer::where('tenant_id', $tenantId)->where('is_isolated', true)->where('updated_at', '<', Carbon::now()->subDays(30))->count();
                $isolir = Customer::where('tenant_id', $tenantId)->where('is_isolated', true)->count();
                $cuti = Customer::where('tenant_id', $tenantId)->where('status', 'inactive')->count();
                
                // Average Lifetime
                $customers = Customer::where('tenant_id', $tenantId)->select('created_at', 'updated_at', 'status')->get();
                $totalMonths = 0; $count = 0;
                foreach ($customers as $c) {
                    $end = $c->status == 'active' ? Carbon::now() : $c->updated_at;
                    $diff = $c->created_at->diffInMonths($end);
                    $totalMonths += max(1, $diff);
                    $count++;
                }
                $avgLife = $count > 0 ? round($totalMonths / $count, 1) : 1;
                $retention = 100 - $churnRate;
                
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'churn_rate' => $churnRate,
                        'suspend_long' => $suspendLong,
                        'recovery' => 0,
                        'avg_life' => $avgLife,
                        'isolir' => $isolir,
                        'cuti' => $cuti,
                        'retention' => $retention,
                    ]
                ]);

            case 'churn_trend':
                $start = Carbon::now()->subMonths(11)->startOfMonth();
                $months = [];
                for ($i=0; $i<12; $i++) {
                    $m = $start->copy()->addMonths($i);
                    $period = $m->format('Y-m');
                    $activeS = max(1, Customer::where('tenant_id', $tenantId)->where('status', 'active')->where('is_isolated', false)->where('created_at', '<=', $m->copy()->subMonth()->endOfMonth())->count());
                    $churned = Customer::where('tenant_id', $tenantId)->where('status', 'inactive')->whereBetween('updated_at', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->count();
                    $months[] = ['bulan' => $period, 'rate' => round(($churned / $activeS) * 100, 1)];
                }
                return response()->json(['status' => 'success', 'data' => $months]);

            case 'net_revenue_detail':
                $mrr = Customer::where('tenant_id', $tenantId)->where('status', 'active')->where('is_isolated', false)->with('package')->get()->sum(function ($c) { return $c->custom_price ?? $c->package?->price ?? 0; });
                $kotor = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$mStart, $mEnd])->sum('amount')
                       + \App\Models\OtherIncome::where('tenant_id', $tenantId)->whereBetween('income_date', [$mStart, $mEnd])->sum('amount');
                $fee_sales = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$mStart, $mEnd])->sum('sales_fee_amount');
                $fee_kolektor = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$mStart, $mEnd])->sum('collector_fee_amount');
                
                $ops = Expense::where('tenant_id', $tenantId)->where('status', 'active')->whereBetween('expense_date', [$mStart, $mEnd])->sum('amount');
                $ppn = Expense::where('tenant_id', $tenantId)->where('status', 'active')->where('category', 'Pajak')->whereBetween('expense_date', [$mStart, $mEnd])->sum('amount');
                $deviden = Expense::where('tenant_id', $tenantId)->where('status', 'active')->where('category', 'Deviden')->whereBetween('expense_date', [$mStart, $mEnd])->sum('amount');
                
                $total_pengeluaran = $fee_sales + $fee_kolektor + $ops;
                $net_revenue = $kotor - $total_pengeluaran;
                
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'mrr' => $mrr,
                        'kotor' => $kotor,
                        'fee_sales' => $fee_sales,
                        'fee_kolektor' => $fee_kolektor,
                        'ppn' => $ppn,
                        'ops' => $ops,
                        'total_pengeluaran' => $total_pengeluaran,
                        'net_revenue' => $net_revenue,
                        'deviden' => $deviden,
                    ]
                ]);

            case 'management_summary':
                // Handled via net_revenue_detail basically, but let's implement outstanding and avg_days
                $mrr = Customer::where('tenant_id', $tenantId)->where('status', 'active')->where('is_isolated', false)->with('package')->get()->sum(function ($c) { return $c->custom_price ?? $c->package?->price ?? 0; });
                $kotor = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$mStart, $mEnd])->sum('amount')
                       + \App\Models\OtherIncome::where('tenant_id', $tenantId)->whereBetween('income_date', [$mStart, $mEnd])->sum('amount');
                
                $totalOut = MonthlyBalance::where('tenant_id', $tenantId)->where('balance', '>', 0)->sum('balance');
                
                // Collection efficiency
                $expected = $mrr;
                $eff = $expected > 0 ? round(($kotor / $expected) * 100, 1) : 0;
                
                $pengeluaran = Expense::where('tenant_id', $tenantId)->where('status', 'active')->whereBetween('expense_date', [$mStart, $mEnd])->sum('amount');
                $fees = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$mStart, $mEnd])->sum(DB::raw('sales_fee_amount + collector_fee_amount'));
                $totalPeng = $pengeluaran + $fees;
                
                $net = $kotor - $totalPeng;
                $deviden = Expense::where('tenant_id', $tenantId)->where('status', 'active')->where('category', 'Deviden')->whereBetween('expense_date', [$mStart, $mEnd])->sum('amount');
                $kasDitahan = $net - $deviden;

                $avgDays = 5; // Simplified
                $arpu = max(1, Customer::where('tenant_id', $tenantId)->where('status', 'active')->where('is_isolated', false)->count());
                $arpu = round($kotor / $arpu);
                $ltv = $arpu * 12; // Simplified
                
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'mrr' => $mrr,
                        'kotor' => $kotor,
                        'pengeluaran' => $totalPeng,
                        'net_revenue' => $net,
                        'deviden' => $deviden,
                        'kas_ditahan' => $kasDitahan,
                        'collection_eff' => $eff,
                        'avg_days_to_pay' => $avgDays,
                        'total_outstanding' => $totalOut,
                        'arpu' => $arpu,
                        'avg_lifetime' => 12,
                        'ltv' => $ltv
                    ]
                ]);

            case 'aging_analysis':
                $now = Carbon::now();
                $unpaidBalances = MonthlyBalance::where('tenant_id', $tenantId)
                    ->where('status', '!=', 'paid')
                    ->select('period', 'balance as amount')
                    ->get();
                
                $aging = [
                    '0_30' => ['label' => '0-30 Hari', 'amount' => 0, 'count' => 0],
                    '31_60' => ['label' => '31-60 Hari', 'amount' => 0, 'count' => 0],
                    '61_90' => ['label' => '61-90 Hari', 'amount' => 0, 'count' => 0],
                    '90_plus' => ['label' => '>90 Hari', 'amount' => 0, 'count' => 0],
                ];

                foreach ($unpaidBalances as $bal) {
                    if (!$bal->period) continue;
                    $dueDate = Carbon::parse($bal->period . '-01')->addMonth();
                    $days = $dueDate->diffInDays($now, false);
                    if ($days < 0) $days = 0;
                    
                    if ($days <= 30) { $aging['0_30']['amount'] += $bal->amount; $aging['0_30']['count']++; }
                    elseif ($days <= 60) { $aging['31_60']['amount'] += $bal->amount; $aging['31_60']['count']++; }
                    elseif ($days <= 90) { $aging['61_90']['amount'] += $bal->amount; $aging['61_90']['count']++; }
                    else { $aging['90_plus']['amount'] += $bal->amount; $aging['90_plus']['count']++; }
                }
                
                return response()->json(['status' => 'success', 'data' => array_values($aging)]);

            case 'revenue_by_area':
                $areas = DB::table('payments')
                    ->join('customers', 'payments.customer_id', '=', 'customers.id')
                    ->leftJoin('areas', 'customers.area_id', '=', 'areas.id')
                    ->where('payments.tenant_id', $tenantId)
                    ->whereBetween('payments.payment_date', [$mStart, $mEnd])
                    ->select(DB::raw('COALESCE(areas.name, \'Tanpa Area\') as area_name'), DB::raw('SUM(payments.amount) as revenue'), DB::raw('COUNT(DISTINCT payments.customer_id) as cust_count'))
                    ->groupBy('area_name')
                    ->orderByDesc('revenue')
                    ->limit(15)
                    ->get();
                return response()->json(['status' => 'success', 'data' => $areas]);

            case 'revenue_by_package':
                $packages = DB::table('payments')
                    ->join('customers', 'payments.customer_id', '=', 'customers.id')
                    ->leftJoin('packages', 'customers.package_id', '=', 'packages.id')
                    ->where('payments.tenant_id', $tenantId)
                    ->whereBetween('payments.payment_date', [$mStart, $mEnd])
                    ->select(DB::raw('COALESCE(packages.name, \'Tanpa Paket\') as paket_name'), DB::raw('SUM(payments.amount) as revenue'), DB::raw('COUNT(DISTINCT payments.customer_id) as cust_count'))
                    ->groupBy('paket_name')
                    ->orderByDesc('revenue')
                    ->limit(15)
                    ->get();
                return response()->json(['status' => 'success', 'data' => $packages]);

            case 'revenue_expense_trend':
                $start = Carbon::now()->subMonths(11)->startOfMonth();
                $months = [];
                for ($i=0; $i<12; $i++) {
                    $m = $start->copy()->addMonths($i);
                    $period = $m->format('Y-m');
                    $rev = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->sum('amount')
                         + \App\Models\OtherIncome::where('tenant_id', $tenantId)->whereBetween('income_date', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->sum('amount');
                    $exp = Expense::where('tenant_id', $tenantId)->where('status', 'active')->whereBetween('expense_date', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->sum('amount');
                    $fees = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->sum(DB::raw('sales_fee_amount + collector_fee_amount'));
                    $expTotal = $exp + $fees;
                    
                    $months[] = ['bulan' => $period, 'revenue' => $rev, 'expense' => $expTotal, 'net' => $rev - $expTotal];
                }
                return response()->json(['status' => 'success', 'data' => $months]);

            case 'collection_trend':
                $hidden = HiddenMonth::where('tenant_id', $tenantId)->pluck('periode')->toArray();
                $start = Carbon::now()->subMonths(11)->startOfMonth();
                $months = [];
                for ($i=0; $i<12; $i++) {
                    $m = $start->copy()->addMonths($i);
                    $periode = $m->format('Y-m');
                    if (in_array($periode, $hidden)) continue;
                    
                    $col = Payment::where('tenant_id', $tenantId)->whereBetween('payment_date', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->sum('amount');
                    $expected = Customer::where('tenant_id', $tenantId)->where('status', 'active')->where('is_isolated', false)->where('created_at', '<=', $m->copy()->endOfMonth())->with('package')->get()->sum(function ($c) { return $c->custom_price ?? $c->package?->price ?? 0; });
                    $expected = max(1, $expected);
                    
                    $months[] = [
                        'bulan' => $periode,
                        'collected' => $col,
                        'expected' => $expected,
                        'efficiency' => round(($col / $expected) * 100, 1),
                    ];
                }
                return response()->json(['status' => 'success', 'data' => $months]);

            default:
                return response()->json(['status' => 'error', 'message' => 'Action tidak dikenal: ' . $action]);
        }
    }
}
