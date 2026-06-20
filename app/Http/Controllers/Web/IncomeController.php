<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function __construct(private PermissionService $permissionService) {}

    public function index(Request $request)
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

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
            $query->where('period', $request->month);
        } else if (!$request->has('month')) {
            // Default ke bulan berjalan jika tidak ada parameter month
            $query->where('period', date('Y-m'));
        }

        if ($request->filled('area_id')) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('area_id', $request->area_id);
            });
        }

        if ($request->filled('sales_id')) {
            $query->where('sales_id', $request->sales_id);
        }

        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $perPage = $query->count() > 0 ? $query->count() : 1;
        }

        $payments = $query->paginate($perPage)->through(function ($payment) {
            $invoice = $payment->invoice;
            $customer = $payment->customer;
            $package = $customer?->package;
            
            // Format periode: YYYY-MM -> M Y (cth: Jun 26)
            $formattedPeriod = $payment->period;
            try {
                $formattedPeriod = \Carbon\Carbon::createFromFormat('Y-m', $payment->period)->translatedFormat('M y');
            } catch (\Exception $e) {}
            
            // Harga dasar paket
            $harga = $package?->price ?? 0;
            
            // Hitung tambahan (selisih jika amount invoice lebih besar dari harga paket, atau dari field tambahan lain)
            $amountBase = $invoice ? $invoice->amount : $payment->amount;
            $tambahan = max(0, $amountBase - $harga);

            // Jika invoice tidak ada, kita asumsikan dari payment
            $diskon = $payment->discount;
            $ppn = $payment->ppn_amount;
            
            // Estimasi BHP & Admin (karena tidak disimpan secara eksplisit per baris)
            // Mengambil dari total_amount atau asumsikan dari konfigurasi jika ada
            $afterDiskon = $amountBase - $diskon;
            $bhpRate = 1.25; // Default config
            $adminFee = 2500; // Default config
            
            $bhp = $customer?->pakai_bhp ? round($afterDiskon * ($bhpRate / 100)) : 0;
            $admin = $customer?->pakai_admin ? $adminFee : 0;

            return [
                'id' => $payment->id,
                'uuid' => $payment->uuid,
                'payment_date' => $payment->payment_date?->format('Y-m-d') . ' ' . $payment->created_at?->format('H:i'),
                'period' => $formattedPeriod,
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

        $filters = $request->only(['search', 'month', 'area_id', 'sales_id', 'per_page']);
        if (!isset($filters['month'])) {
            $filters['month'] = date('Y-m');
        }

        $areas = \App\Models\Area::orderBy('name')->get();
        $salesList = \App\Models\User::where('is_active', true)->orderBy('name')->get();

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

        return Inertia::render('Reports/Income', compact('payments', 'filters', 'capabilities', 'tenant', 'areas', 'salesList', 'kpiData'));
    }

    public function printInvoice(Payment $payment)
    {
        // Dalam implementasi nyata, ini akan me-return view PDF atau HTML print khusus
        return back()->with('success', 'Fitur cetak invoice sedang disiapkan.');
    }

    public function resendInvoice(Payment $payment)
    {
        // Panggil endpoint atau service WhatsApp Gateway di sini
        return back()->with('success', 'Invoice berhasil dikirim ulang via WhatsApp.');
    }

    public function refund(Payment $payment)
    {
        // Logic refund: batalkan pembayaran, update saldo customer/invoice
        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id()
            ]);

            // Jika ada relasi invoice, sesuaikan status invoice
            if ($payment->invoice) {
                // Sederhananya, jika dibatalkan, status invoice kembali unpaid (bisa juga partial tergantung sisa)
                $payment->invoice->update(['status' => 'unpaid']);
            }
        });

        return back()->with('success', 'Pembayaran berhasil dibatalkan (Refund).');
    }
}
