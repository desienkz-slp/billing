<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IncomeApiController extends Controller
{
    private $permissionService;
    public function __construct(PermissionService $permissionService) {
        $this->permissionService = $permissionService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);

        $query = Payment::with(['customer.area', 'customer.package', 'sales', 'collector', 'invoice'])
            ->where('status', 'paid')
            ->orderByDesc('payment_date')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereHas('customer', function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(username) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('month')) {
            $month = $request->month;
            $query->whereYear('payment_date', substr($month, 0, 4))
                  ->whereMonth('payment_date', substr($month, 5, 2));
        } else if (!$request->has('month')) {
            $month = date('Y-m');
            $query->whereYear('payment_date', substr($month, 0, 4))
                  ->whereMonth('payment_date', substr($month, 5, 2));
        }

        if ($request->filled('area_id')) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('area_id', $request->area_id);
            });
        }

        if ($request->filled('sales_id')) {
            $query->where('sales_id', $request->sales_id);
        }

        if ($request->filled('collected_by')) {
            $query->where('collected_by', $request->collected_by);
        }

        // Calculate KPI Data for all filtered records
        $kpiQuery = clone $query;
        $allFiltered = $kpiQuery->get();
        
        $kpiData = [
            'Total Harga' => 0,
            'Total Diskon' => 0,
            'Total Tambahan' => 0,
            'Total PPN' => 0,
            'Total BHP USO' => 0,
            'Total Admin' => 0,
            'Total Terima' => 0,
        ];

        foreach ($allFiltered as $p) {
            $inv = $p->invoice;
            $cust = $p->customer;
            $pkg = $cust?->package;
            
            $h = $pkg?->price ?? 0;
            $amtBase = $inv ? $inv->amount : $p->amount;
            $t = max(0, $amtBase - $h);
            $d = $p->discount;
            $ppn = $p->ppn_amount;
            
            $afterD = $amtBase - $d;
            $bhp = $cust?->pakai_bhp ? round($afterD * (1.25 / 100)) : 0;
            $admin = $cust?->pakai_admin ? 2500 : 0;

            $kpiData['Total Harga'] += $h;
            $kpiData['Total Diskon'] += $d;
            $kpiData['Total Tambahan'] += $t;
            $kpiData['Total PPN'] += $ppn;
            $kpiData['Total BHP USO'] += $bhp;
            $kpiData['Total Admin'] += $admin;
            $kpiData['Total Terima'] += $p->paid_amount;
        }

        $perPage = $request->input('per_page', 25);
        $paginator = $query->paginate($perPage);

        $mappedData = $paginator->map(function ($payment) {
            $invoice = $payment->invoice;
            $customer = $payment->customer;
            $package = $customer?->package;
            
            $formattedPeriod = $payment->period;
            $monthCount = 1;
            
            // Cek apakah invoice memiliki multi-bulan di notes
            if ($invoice && !empty($invoice->notes) && str_contains($invoice->notes, '20')) {
                $periods = explode(',', $invoice->notes);
                // Validasi sederhana apakah format Y-m
                $validPeriods = array_filter($periods, fn($p) => preg_match('/^\d{4}-\d{2}$/', trim($p)));
                
                if (count($validPeriods) > 1) {
                    $monthCount = count($validPeriods);
                    sort($validPeriods);
                    try {
                        $first = \Carbon\Carbon::createFromFormat('Y-m', $validPeriods[0]);
                        $last = \Carbon\Carbon::createFromFormat('Y-m', end($validPeriods));
                        
                        if ($first->year === $last->year) {
                            $formattedPeriod = $first->translatedFormat('M') . ' - ' . $last->translatedFormat('M y');
                        } else {
                            $formattedPeriod = $first->translatedFormat('M y') . ' - ' . $last->translatedFormat('M y');
                        }
                    } catch (\Exception $e) {}
                } else {
                    try {
                        $formattedPeriod = \Carbon\Carbon::createFromFormat('Y-m', $payment->period)->translatedFormat('M y');
                    } catch (\Exception $e) {}
                }
            } else {
                try {
                    $formattedPeriod = \Carbon\Carbon::createFromFormat('Y-m', $payment->period)->translatedFormat('M y');
                } catch (\Exception $e) {}
            }
            
            $harga = $package?->price ?? 0;
            $amountBase = $invoice ? $invoice->amount : $payment->amount;
            
            // Tambahan adalah sisa dari harga dikali jumlah bulan
            $expectedBase = $harga * $monthCount;
            $tambahan = max(0, $amountBase - $expectedBase);
            
            $diskon = $payment->discount;
            $ppn = $payment->ppn_amount;
            
            $afterDiskon = $amountBase - $diskon;
            $bhpRate = 1.25; 
            $adminFee = 2500; 
            
            $bhp = $customer?->pakai_bhp ? round($afterDiskon * ($bhpRate / 100)) : 0;
            $admin = $customer?->pakai_admin ? $adminFee : 0;

            return [
                'id' => $payment->id,
                'uuid' => $payment->uuid,
                'payment_date' => $payment->payment_date?->format('Y-m-d') . ' ' . $payment->created_at?->format('H:i'),
                'period' => $formattedPeriod,
                'customer_id' => $customer?->id,
                'customer_name' => $customer?->name ?? '-',
                'package_name' => $package?->name ?? '-',
                'sales_name' => $payment->sales?->name ?? '-',
                'payment_method' => $payment->payment_method,
                'harga' => $harga,
                'diskon' => $diskon,
                'tambahan' => $tambahan,
                'ppn' => $ppn,
                'bhp_uso' => $bhp,
                'admin' => $admin,
                'terima' => $payment->paid_amount,
                'status' => $payment->status,
                'created_by_name' => $payment->collector?->name ?? 'Sistem',
            ];
        });

        $areas = \App\Models\Area::select('id', 'name')->get();
        $sales = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'sales');
        })->orWhere('is_default_sales', true)->select('id', 'name')->get();
        $collectors = \App\Models\User::select('id', 'name')->get();

        return response()->json([
            'data' => $mappedData,
            'kpi' => $kpiData,
            'capabilities' => $capabilities,
            'filter_options' => [
                'areas' => $areas,
                'sales' => $sales,
                'collectors' => $collectors,
            ],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        ]);
    }
}
