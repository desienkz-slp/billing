<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Package;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(private PermissionService $permissionService) {}

    public function statistics(Request $request)
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        // Monthly revenue (12 months)
        $revenueMonthly = Payment::where('status', 'paid')
            ->where('payment_date', '>=', now()->subMonths(12)->startOfMonth())
            ->selectRaw("to_char(payment_date, 'YYYY-MM') as period, COUNT(*) as count, SUM(paid_amount) as total")
            ->groupByRaw("to_char(payment_date, 'YYYY-MM')")
            ->orderBy('period')
            ->get();

        // Customer growth
        $customerGrowth = Customer::where('registration_date', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("to_char(registration_date, 'YYYY-MM') as period, COUNT(*) as count")
            ->groupByRaw("to_char(registration_date, 'YYYY-MM')")
            ->orderBy('period')
            ->get();

        // Package distribution
        $packageDist = Customer::where('status', 'active')
            ->selectRaw('package_id, COUNT(*) as count')
            ->groupBy('package_id')
            ->with('package')
            ->get();

        // Payment method stats
        $methodStats = Payment::where('status', 'paid')
            ->whereYear('payment_date', now()->year)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(paid_amount) as total')
            ->groupBy('payment_method')
            ->get();

        return Inertia::render('Reports/Statistics', compact(
            'capabilities', 'revenueMonthly', 'customerGrowth', 'packageDist', 'methodStats'
        ));
    }

    public function finance(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $year = $request->get('year', now()->year);

        // Monthly P&L
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenue = Payment::where('status', 'paid')
                ->whereMonth('payment_date', $m)->whereYear('payment_date', $year)
                ->sum('paid_amount');
            
            $otherIncomes = \App\Models\OtherIncome::whereMonth('income_date', $m)->whereYear('income_date', $year)
                ->sum('amount');
            
            $revenue += $otherIncomes;

            $ppn = Payment::where('status', 'paid')
                ->whereMonth('payment_date', $m)->whereYear('payment_date', $year)
                ->sum('ppn_amount');
            $pengeluaran = \App\Models\Expense::where('status', 'active')
                ->whereMonth('expense_date', $m)->whereYear('expense_date', $year)
                ->sum('amount');

            $monthlyData[] = [
                'month' => \Carbon\Carbon::create($year, $m)->locale('id')->isoFormat('MMM'),
                'pendapatan' => $revenue,
                'ppn' => $ppn,
                'pengeluaran' => $pengeluaran,
            ];
        }

        $yearTotal = [
            'pendapatan' => collect($monthlyData)->sum('pendapatan'),
            'ppn' => collect($monthlyData)->sum('ppn'),
            'pengeluaran' => collect($monthlyData)->sum('pengeluaran'),
        ];

        return Inertia::render('Reports/Finance', compact('capabilities', 'monthlyData', 'yearTotal', 'year'));
    }

    public function detailPendapatan(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $month = $request->get('month', now()->format('Y-m'));

        $payments = Payment::with(['customer.package', 'customer.area', 'collector'])
            ->where('status', 'paid')
            ->whereRaw("to_char(payment_date, 'YYYY-MM') = ?", [$month])
            ->orderByDesc('payment_date')->orderByDesc('id')
            ->get();

        $totals = [
            'amount' => collect($payments)->sum('paid_amount'),
            'count' => count($payments),
        ];

        $areas = Area::orderBy('name')->get();

        return Inertia::render('Reports/DetailPendapatan', compact('capabilities', 'payments', 'totals', 'month', 'areas'));
    }

    public function pengeluaran(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $month = $request->get('month', now()->format('Y-m'));

        $expenses = \App\Models\Expense::with('creator')
            ->whereRaw("to_char(expense_date, 'YYYY-MM') = ?", [$month])
            ->where('status', 'active')
            ->orderByDesc('id')->get();

        $total = $expenses->sum('amount');
        $categories = ['Operasional', 'Marketing', 'Alat/Material', 'Pajak', 'Deviden', 'Lainnya'];

        return Inertia::render('Reports/Pengeluaran', compact('capabilities', 'expenses', 'total', 'month', 'categories'));
    }

    public function storePengeluaran(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|integer|min:1',
        ]);
        $data['tenant_id'] = $request->user()->tenant_id;
        $data['created_by'] = $request->user()->id;
        $data['expense_date'] = now()->toDateString();
        $data['status'] = 'active';
        \App\Models\Expense::create($data);
        return back()->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    // ===== TAX =====
    public function tax(Request $request)
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;
        $month = $request->get('month', now()->format('Y-m'));

        $paymentsCollection = Payment::with(['customer.package'])
            ->where('status', 'paid')
            ->whereRaw("to_char(payment_date, 'YYYY-MM') = ?", [$month])
            ->orderByDesc('payment_date')->get()
            ->map(function ($payment) {
                $customer = $payment->customer;
                $package = $customer?->package;
                
                $harga = $package?->price ?? 0;
                // If invoice exists, take its amount as base price to be consistent
                $amountBase = $payment->invoice ? $payment->invoice->amount : $payment->amount;
                $diskon = $payment->discount;
                $afterDiskon = max(0, $amountBase - $diskon);
                
                $bhpRate = 1.25;
                $adminFee = 2500;
                
                $bhp = $customer?->pakai_bhp ? round($afterDiskon * ($bhpRate / 100)) : 0;
                $admin = $customer?->pakai_admin ? $adminFee : 0;

                return [
                    'id' => $payment->id,
                    'payment_date' => $payment->payment_date?->format('d/m/Y H:i'),
                    'customer_name' => $customer?->name ?? '-',
                    'package_name' => $package?->name ?? '-',
                    'ppn' => (float)$payment->ppn_amount,
                    'bhp_uso' => (float)$bhp,
                    'admin' => (float)$admin,
                ];
            });

        $totalPpn = $paymentsCollection->sum('ppn');
        $totalBhp = $paymentsCollection->sum('bhp_uso');
        $totalAdmin = $paymentsCollection->sum('admin');

        // Hanya tampilkan data yang memiliki nilai pajak/biaya admin setidaknya salah satu
        $payments = $paymentsCollection->filter(fn($p) => $p['ppn'] > 0 || $p['bhp_uso'] > 0 || $p['admin'] > 0)->values();

        return Inertia::render('Reports/Tax', compact('capabilities', 'payments', 'totalPpn', 'totalBhp', 'totalAdmin', 'month'));
    }

    // ===== Total =====
    public function total(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;
        $month = $request->get('month', now()->format('Y-m'));

        $pendapatan = Payment::where('status', 'paid')
            ->whereRaw("to_char(payment_date, 'YYYY-MM') = ?", [$month])->sum('paid_amount');
        $pendapatanLain = \App\Models\OtherIncome::whereRaw("to_char(income_date, 'YYYY-MM') = ?", [$month])->sum('amount');
        $ppn = Payment::where('status', 'paid')
            ->whereRaw("to_char(payment_date, 'YYYY-MM') = ?", [$month])->sum('ppn_amount');
        $txnCount = Payment::where('status', 'paid')
            ->whereRaw("to_char(payment_date, 'YYYY-MM') = ?", [$month])->count();
        $pengeluaran = \App\Models\Expense::where('status', 'active')
            ->whereRaw("to_char(expense_date, 'YYYY-MM') = ?", [$month])->sum('amount');
        $setoran = \App\Models\Deposit::where('status', 'active')
            ->whereRaw("to_char(deposit_date, 'YYYY-MM') = ?", [$month])->sum('amount');

        // Calculate fees and remaining un-deposited amounts similar to the Setoran page
        $salesUsers = \App\Models\User::where('is_active', true)
            ->with(['role', 
                'collectedPayments' => function($q) use ($month) {
                    $q->where('status', 'paid')
                      ->whereRaw("to_char(payment_date, 'YYYY-MM') = ?", [$month]);
                },
                'deposits' => function($q) use ($month) {
                    $q->where('status', 'active')
                      ->whereRaw("to_char(deposit_date, 'YYYY-MM') = ?", [$month]);
                },
                'expenses' => function($q) use ($month) {
                    $q->where('status', 'active')
                      ->whereRaw("to_char(expense_date, 'YYYY-MM') = ?", [$month]);
                }
            ])
            ->get()
            ->map(function($u) {
                $uang_diterima = $u->collectedPayments->sum('paid_amount');
                $txn = $u->collectedPayments->count();
                $fee = ($u->fee_persen / 100 * $uang_diterima) + ($u->fee_fix * $txn);
                $pengeluaran = $u->expenses->sum('amount');
                $sudah_disetor = $u->deposits->sum('amount');
                
                $harus_disetor = $uang_diterima - $fee - $pengeluaran;
                $sisa = $harus_disetor - $sudah_disetor;
                return [
                    'fee' => $fee,
                    'sisa' => $sisa,
                ];
            });

        $total_fee = $salesUsers->sum('fee');
        $belum_disetor = $salesUsers->sum('sisa');

        return Inertia::render('Reports/Total', compact('capabilities', 'pendapatan', 'pendapatanLain', 'ppn', 'txnCount', 'pengeluaran', 'setoran', 'month', 'tenant', 'total_fee', 'belum_disetor'));
    }

    // ===== Arus Kas =====
    public function cashflow(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $month = $request->get('month', now()->format('Y-m'));

        // ============================
        // 1. ARUS MASUK
        // ============================
        $payments = Payment::where('status', 'paid')
            ->whereRaw("to_char(payment_date, 'YYYY-MM') = ?", [$month])
            ->get();

        $penerimaan_metode = $payments->groupBy('payment_method')->map(function ($items, $method) {
            return [
                'nama' => ucfirst($method ?: 'Lainnya'),
                'jumlah_trx' => $items->count(),
                'total' => $items->sum('paid_amount'),
            ];
        })->values();

        $total_penerimaan = $payments->sum('paid_amount');

        $otherIncomes = \App\Models\OtherIncome::whereRaw("to_char(income_date, 'YYYY-MM') = ?", [$month])
            ->get();
        $total_pendapatan_lain = $otherIncomes->sum('amount');
        $pendapatan_lain_items = $otherIncomes->groupBy('category')->map(function ($items, $category) {
             return [
                 'nama' => $category ?: 'Lain-lain',
                 'jumlah_trx' => $items->count(),
                 'total' => $items->sum('amount'),
             ];
        })->values();

        $total_arus_masuk = $total_penerimaan + $total_pendapatan_lain;

        // ============================
        // 2. ARUS KELUAR
        // ============================
        $ppn_tercatat = $payments->sum('ppn_amount');

        $salesFees = Payment::with('sales:id,name')
            ->where('status', 'paid')
            ->whereRaw("to_char(payment_date, 'YYYY-MM') = ?", [$month])
            ->where('sales_fee_amount', '>', 0)
            ->get();
            
        $collectorFees = Payment::with('collector:id,name')
            ->where('status', 'paid')
            ->whereRaw("to_char(payment_date, 'YYYY-MM') = ?", [$month])
            ->where('collector_fee_amount', '>', 0)
            ->get();

        $feeUsers = [];
        foreach ($salesFees as $sf) {
            $name = $sf->sales ? $sf->sales->name : 'Unknown';
            if (!isset($feeUsers[$name])) $feeUsers[$name] = 0;
            $feeUsers[$name] += $sf->sales_fee_amount;
        }
        foreach ($collectorFees as $cf) {
            $name = $cf->collector ? $cf->collector->name : 'Unknown';
            if (!isset($feeUsers[$name])) $feeUsers[$name] = 0;
            $feeUsers[$name] += $cf->collector_fee_amount;
        }

        $pembayaran_fee = collect($feeUsers)->map(function ($total, $user_name) {
            return [
                'user' => $user_name,
                'total' => $total
            ];
        })->values();
        $total_fee = $pembayaran_fee->sum('total');

        $expenses = \App\Models\Expense::with('creator:id,name')
            ->where('status', 'active')
            ->whereRaw("to_char(expense_date, 'YYYY-MM') = ?", [$month])
            ->get();

        $pengeluaran_per_user = $expenses->groupBy('created_by')->map(function ($items, $user_id) {
            $first = $items->first();
            return [
                'user' => $first->creator ? $first->creator->name : 'Unknown',
                'jumlah_item' => $items->count(),
                'total' => $items->sum('amount')
            ];
        })->values();
        $total_pengeluaran_user = $pengeluaran_per_user->sum('total');

        $total_arus_keluar = $total_fee + $total_pengeluaran_user + $ppn_tercatat;
        $arus_kas_bersih = $total_arus_masuk - $total_arus_keluar;

        // ============================
        // 3. DATA BULAN SEBELUMNYA (Growth)
        // ============================
        $lastMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->subMonth()->format('Y-m');
        
        $lm_payments = Payment::where('status', 'paid')
            ->whereRaw("to_char(payment_date, 'YYYY-MM') = ?", [$lastMonth])
            ->get();
        $lm_arus_masuk = $lm_payments->sum('paid_amount') + \App\Models\OtherIncome::whereRaw("to_char(income_date, 'YYYY-MM') = ?", [$lastMonth])->sum('amount');
        $lm_arus_keluar = $lm_payments->sum('sales_fee_amount') + $lm_payments->sum('collector_fee_amount') + $lm_payments->sum('ppn_amount') + \App\Models\Expense::where('status', 'active')->whereRaw("to_char(expense_date, 'YYYY-MM') = ?", [$lastMonth])->sum('amount');
        $lm_arus_kas_bersih = $lm_arus_masuk - $lm_arus_keluar;

        $growth = 0;
        if ($lm_arus_kas_bersih > 0) {
            $growth = (($arus_kas_bersih - $lm_arus_kas_bersih) / $lm_arus_kas_bersih) * 100;
        } elseif ($lm_arus_kas_bersih <= 0 && $arus_kas_bersih > 0) {
            $growth = 100;
        }

        // ============================
        // 4. RINCIAN TRANSAKSI (LEDGER)
        // ============================
        $transactions = collect();
        foreach ($payments as $p) {
            $time = $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('H:i:s') : '00:00:00';
            $date = \Carbon\Carbon::parse($p->payment_date)->format('Y-m-d');
            $datetime = $date . ' ' . $time;

            // Income
            $transactions->push([
                'tanggal' => $datetime,
                'keterangan' => 'Penerimaan Pelanggan via ' . ucfirst($p->payment_method),
                'referensi' => $p->transaction_id ?: '-',
                'masuk' => $p->paid_amount,
                'keluar' => 0,
            ]);
            // PPN Out
            if ($p->ppn_amount > 0) {
                $transactions->push([
                    'tanggal' => $datetime,
                    'keterangan' => 'Potongan PPN Tercatat',
                    'referensi' => $p->transaction_id ?: '-',
                    'masuk' => 0,
                    'keluar' => $p->ppn_amount,
                ]);
            }
            // Sales Fee Out
            if ($p->sales_fee_amount > 0) {
                $name = $p->sales ? $p->sales->name : 'Unknown';
                $transactions->push([
                    'tanggal' => $datetime,
                    'keterangan' => 'Pembayaran Fee Sales (' . $name . ')',
                    'referensi' => $p->transaction_id ?: '-',
                    'masuk' => 0,
                    'keluar' => $p->sales_fee_amount,
                ]);
            }
            // Collector Fee Out
            if ($p->collector_fee_amount > 0) {
                $name = $p->collector ? $p->collector->name : 'Unknown';
                $transactions->push([
                    'tanggal' => $datetime,
                    'keterangan' => 'Pembayaran Fee Collector (' . $name . ')',
                    'referensi' => $p->transaction_id ?: '-',
                    'masuk' => 0,
                    'keluar' => $p->collector_fee_amount,
                ]);
            }
        }

        foreach ($otherIncomes as $oi) {
            $time = $oi->created_at ? \Carbon\Carbon::parse($oi->created_at)->format('H:i:s') : '00:00:00';
            $date = \Carbon\Carbon::parse($oi->income_date)->format('Y-m-d');
            $transactions->push([
                'tanggal' => $date . ' ' . $time,
                'keterangan' => 'Pendapatan Lain: ' . $oi->category . ($oi->notes ? ' (' . $oi->notes . ')' : ''),
                'referensi' => '-',
                'masuk' => $oi->amount,
                'keluar' => 0,
            ]);
        }

        foreach ($expenses as $ex) {
            $time = $ex->created_at ? \Carbon\Carbon::parse($ex->created_at)->format('H:i:s') : '00:00:00';
            $date = \Carbon\Carbon::parse($ex->expense_date)->format('Y-m-d');
            $name = $ex->creator ? $ex->creator->name : 'Unknown';
            $transactions->push([
                'tanggal' => $date . ' ' . $time,
                'keterangan' => 'Pengeluaran (' . $name . '): ' . $ex->category . ($ex->description ? ' (' . $ex->description . ')' : ''),
                'referensi' => '-',
                'masuk' => 0,
                'keluar' => $ex->amount,
            ]);
        }

        // Sort by date
        $transactions = $transactions->sortBy('tanggal')->values();

        // Calculate Running Balance
        $startOfMonthDate = \Carbon\Carbon::parse($month . '-01')->startOfDay();
        
        $pastPaymentsIn = Payment::where('status', 'paid')->where('payment_date', '<', $startOfMonthDate)->sum('paid_amount');
        $pastOtherIn = \App\Models\OtherIncome::where('income_date', '<', $startOfMonthDate)->sum('amount');
        $pastPpnOut = Payment::where('status', 'paid')->where('payment_date', '<', $startOfMonthDate)->sum('ppn_amount');
        $pastSalesFeeOut = Payment::where('status', 'paid')->where('payment_date', '<', $startOfMonthDate)->sum('sales_fee_amount');
        $pastCollectorFeeOut = Payment::where('status', 'paid')->where('payment_date', '<', $startOfMonthDate)->sum('collector_fee_amount');
        $pastExpOut = \App\Models\Expense::where('status', 'active')->where('expense_date', '<', $startOfMonthDate)->sum('amount');
        
        $saldo_awal = ($pastPaymentsIn + $pastOtherIn) - ($pastPpnOut + $pastSalesFeeOut + $pastCollectorFeeOut + $pastExpOut);
        
        $runningBalance = $saldo_awal;
        $transactions = $transactions->map(function ($trx) use (&$runningBalance) {
            $runningBalance += $trx['masuk'];
            $runningBalance -= $trx['keluar'];
            $trx['saldo'] = $runningBalance;
            return $trx;
        });

        $reportData = [
            'arus_masuk' => [
                'penerimaan_pelanggan' => [
                    'total' => $total_penerimaan,
                    'metode' => $penerimaan_metode
                ],
                'pendapatan_lain' => [
                    'total' => $total_pendapatan_lain,
                    'items' => $pendapatan_lain_items
                ],
                'total' => $total_arus_masuk
            ],
            'arus_keluar' => [
                'fee_per_user' => [
                    'total' => $total_fee,
                    'users' => $pembayaran_fee
                ],
                'pengeluaran_per_user' => [
                    'total' => $total_pengeluaran_user,
                    'users' => $pengeluaran_per_user
                ],
                'ppn' => $ppn_tercatat,
                'total' => $total_arus_keluar
            ],
            'summary' => [
                'bersih_bulan_ini' => $arus_kas_bersih,
                'bersih_bulan_lalu' => $lm_arus_kas_bersih,
                'last_month_label' => \Carbon\Carbon::createFromFormat('Y-m', $lastMonth)->locale('en')->isoFormat('MMMM YYYY'),
                'growth' => round($growth, 1)
            ],
            'riwayat_transaksi' => [
                'saldo_awal' => $saldo_awal,
                'data' => $transactions
            ]
        ];

        return Inertia::render('Reports/Cashflow', compact('capabilities', 'reportData', 'month'));
    }

    // ===== BATCH C: Fee =====
    public function fee(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenantId = $user->tenant_id;
        $month = $request->get('month', now()->format('Y-m'));

        $start = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $end = \Carbon\Carbon::parse($month . '-01')->endOfMonth();

        // Sales fee
        $salesFees = Payment::where('tenant_id', $tenantId)
            ->whereBetween('payment_date', [$start, $end])
            ->where('status', 'paid')
            ->whereNotNull('sales_id')
            ->groupBy('sales_id')
            ->selectRaw('sales_id as user_id, SUM(sales_fee_amount) as total_fee')
            ->get();

        // Collector fee
        $collectorFees = Payment::where('tenant_id', $tenantId)
            ->whereBetween('payment_date', [$start, $end])
            ->where('status', 'paid')
            ->whereNotNull('collected_by')
            ->groupBy('collected_by')
            ->selectRaw('collected_by as user_id, SUM(collector_fee_amount) as total_fee')
            ->get();

        $userFees = [];
        foreach ($salesFees as $sf) {
            if (!isset($userFees[$sf->user_id])) {
                $userFees[$sf->user_id] = ['sales' => 0, 'collector' => 0, 'total' => 0];
            }
            $userFees[$sf->user_id]['sales'] += $sf->total_fee;
        }
        foreach ($collectorFees as $cf) {
            if (!isset($userFees[$cf->user_id])) {
                $userFees[$cf->user_id] = ['sales' => 0, 'collector' => 0, 'total' => 0];
            }
            $userFees[$cf->user_id]['collector'] += $cf->total_fee;
        }

        $formattedUserFees = [];
        $userIds = array_keys($userFees);
        $users = \App\Models\User::whereIn('id', $userIds)->with('role')->get()->keyBy('id');

        $totalSalesFee = 0;
        $totalCollectorFee = 0;

        foreach ($userFees as $userId => $fee) {
            $fee['total'] = $fee['sales'] + $fee['collector'];
            $totalSalesFee += $fee['sales'];
            $totalCollectorFee += $fee['collector'];
            
            $formattedUserFees[] = [
                'id' => $userId,
                'name' => $users[$userId]->name ?? 'Unknown',
                'role_name' => $users[$userId]->role->name ?? 'Unknown',
                'sales_fee' => $fee['sales'],
                'collector_fee' => $fee['collector'],
                'total_fee' => $fee['total']
            ];
        }

        // Sort by total_fee descending
        usort($formattedUserFees, function($a, $b) {
            return $b['total_fee'] <=> $a['total_fee'];
        });

        $totalKeseluruhan = $totalSalesFee + $totalCollectorFee;

        return Inertia::render('Sales/Fee', [
            'capabilities' => $capabilities,
            'userFees' => $formattedUserFees,
            'month' => $month,
            'totalSalesFee' => $totalSalesFee,
            'totalCollectorFee' => $totalCollectorFee,
            'totalKeseluruhan' => $totalKeseluruhan
        ]);
    }

    // ===== Setoran =====
    public function setoran(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $month = $request->get('month', now()->format('Y-m'));

        $salesUsers = \App\Models\User::where('is_active', true)
            ->with(['role', 
                'collectedPayments' => function($q) use ($month) {
                    $q->where('status', 'paid')
                      ->whereRaw("to_char(payment_date, 'YYYY-MM') = ?", [$month]);
                },
                'deposits' => function($q) use ($month) {
                    $q->where('status', 'active')
                      ->whereRaw("to_char(deposit_date, 'YYYY-MM') = ?", [$month]);
                },
                'expenses' => function($q) use ($month) {
                    $q->where('status', 'active')
                      ->whereRaw("to_char(expense_date, 'YYYY-MM') = ?", [$month]);
                }
            ])
            ->get()
            ->map(function($u) {
                $uang_diterima = $u->collectedPayments->sum('paid_amount');
                $txn = $u->collectedPayments->count();
                $fee = ($u->fee_persen / 100 * $uang_diterima) + ($u->fee_fix * $txn);
                $pengeluaran = $u->expenses->sum('amount');
                $sudah_disetor = $u->deposits->sum('amount');
                
                $harus_disetor = $uang_diterima - $fee - $pengeluaran;
                $sisa = $harus_disetor - $sudah_disetor;

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'role_name' => $u->role ? $u->role->name : '-',
                    'uang_diterima' => $uang_diterima,
                    'fee' => $fee,
                    'pengeluaran' => $pengeluaran,
                    'harus_disetor' => $harus_disetor,
                    'sudah_disetor' => $sudah_disetor,
                    'sisa' => $sisa,
                    'txn' => $txn,
                ];
            })
            // Filter users who have any activity
            ->filter(fn($u) => $u['uang_diterima'] > 0 || $u['sudah_disetor'] > 0 || $u['pengeluaran'] > 0)
            ->values();

        // Calculate globals
        $totalUangDiterima = $salesUsers->sum('uang_diterima');
        $totalTxn = $salesUsers->sum('txn');
        $totalFee = $salesUsers->sum('fee');
        $totalHarusDisetor = $salesUsers->sum('harus_disetor');
        $totalSales = $salesUsers->count();

        return Inertia::render('Sales/Setoran', compact(
            'capabilities', 'salesUsers', 'month', 
            'totalUangDiterima', 'totalTxn', 'totalFee', 'totalHarusDisetor', 'totalSales'
        ));
    }

    public function storeSetoran(Request $request)
    {
        $data = $request->validate([
            'sales_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1',
            'method' => 'required|string|max:30',
            'notes' => 'nullable|string|max:255',
        ]);
        $data['tenant_id'] = $request->user()->tenant_id;
        $data['received_by'] = $request->user()->id;
        $data['deposit_date'] = now()->toDateString();
        $data['status'] = 'active';
        \App\Models\Deposit::create($data);
        return back()->with('success', 'Setoran berhasil dicatat.');
    }

    public function destroySetoran(Request $request, \App\Models\Deposit $deposit)
    {
        $user = $request->user();
        if (!app(\App\Services\PermissionService::class)->userCan($user, 'billing.deposits.cancel')) {
            abort(403);
        }

        $deposit->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Setoran berhasil dibatalkan.');
    }

    // ===== Mitra =====
    public function mitra(Request $request)
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        
        // Fetch all active users with role and customer count
        $allUsers = \App\Models\User::where('is_active', true)
            ->with('role')
            ->withCount('customers')
            ->get();

        // Filter users who have the payment capability (can_bayar -> billing.payments.create)
        $salesData = $allUsers->filter(function($u) {
            return app(\App\Services\PermissionService::class)->userCan($u, 'billing.payments.create');
        })->values()->map(function($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'role_name' => $u->role ? $u->role->name : '-',
                'fee_persen' => $u->fee_persen,
                'fee_fix' => $u->fee_fix,
                'customers_count' => $u->customers_count,
            ];
        });

        return Inertia::render('Sales/Mitra', compact('capabilities', 'salesData'));
    }

    public function mitraDeposits(Request $request, \App\Models\User $user)
    {
        // Must have view permissions or be the user
        $month = $request->get('month'); // e.g. YYYY-MM or null for all periods
        
        $query = \App\Models\Deposit::with(['receiver'])
            ->where('sales_id', $user->id)
            ->orderByDesc('deposit_date')
            ->orderByDesc('id');

        if ($month) {
            $query->whereRaw("to_char(deposit_date, 'YYYY-MM') = ?", [$month]);
        }

        $deposits = $query->get()->map(function($d) {
            return [
                'id' => $d->id,
                'amount' => $d->amount,
                'deposit_date' => $d->deposit_date ? $d->deposit_date->format('d/m/Y') : '-',
                'period_label' => $d->deposit_date ? $d->deposit_date->translatedFormat('F Y') : '-',
                'notes' => $d->notes ?? '—',
                'status' => $d->status,
                'receiver_name' => $d->receiver ? $d->receiver->name : 'admin',
            ];
        });

        return response()->json($deposits);
    }

    // ===== Saldo =====
    public function saldo(Request $request): Response
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);

        // Ambil semua user yang role-nya memiliki is_saldo_limited = true
        $salesUsers = \App\Models\User::where('is_active', true)
            ->whereHas('role', function($q) {
                $q->where('is_saldo_limited', true);
            })
            ->with('role')
            ->get()
            ->map(function($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'role_name' => $u->role ? $u->role->name : '-',
                    'deposit_balance' => (float) $u->deposit_balance,
                ];
            });

        return Inertia::render('Sales/Saldo', compact('capabilities', 'salesUsers'));
    }

    public function addSaldo(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $admin = $request->user();
        
        // TODO: check capability can_manage_saldo

        $amount = $request->amount;
        $balanceBefore = $user->deposit_balance ?? 0;
        $balanceAfter = $balanceBefore + $amount;

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $amount, $balanceBefore, $balanceAfter, $request, $admin) {
            $user->update(['deposit_balance' => $balanceAfter]);

            \App\Models\DepositMutation::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'notes' => $request->notes ?? 'Penambahan saldo oleh ' . $admin->name,
                'created_by' => $admin->id,
            ]);
        });

        return back()->with('success', 'Saldo berhasil ditambahkan.');
    }

    public function deductSaldo(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $admin = $request->user();
        
        $amount = $request->amount;
        $balanceBefore = $user->deposit_balance ?? 0;
        
        if ($balanceBefore < $amount) {
            return back()->withErrors(['amount' => 'Saldo tidak mencukupi untuk pengurangan ini.']);
        }

        $balanceAfter = $balanceBefore - $amount;

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $amount, $balanceBefore, $balanceAfter, $request, $admin) {
            $user->update(['deposit_balance' => $balanceAfter]);

            \App\Models\DepositMutation::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'notes' => $request->notes ?? 'Pengurangan saldo oleh ' . $admin->name,
                'created_by' => $admin->id,
            ]);
        });

        return back()->with('success', 'Saldo berhasil dikurangi.');
    }

    public function saldoHistory(Request $request, \App\Models\User $user)
    {
        $mutations = \App\Models\DepositMutation::with('creator')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get()
            ->map(function($m) {
                return [
                    'id' => $m->id,
                    'type' => $m->type,
                    'amount' => (float) $m->amount,
                    'balance_before' => (float) $m->balance_before,
                    'balance_after' => (float) $m->balance_after,
                    'notes' => $m->notes,
                    'created_at' => $m->created_at->format('d/m/Y H:i'),
                    'creator_name' => $m->creator ? $m->creator->name : 'Sistem',
                ];
            });

        return response()->json($mutations);
    }
}
