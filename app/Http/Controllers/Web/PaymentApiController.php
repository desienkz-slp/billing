<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function getMonths(Customer $customer, Request $request)
    {
        // Pastikan customer milik tenant yang sedang login
        if ($customer->tenant_id !== $request->user()->tenant_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $paidMonths = $this->paymentService->getPaymentMonths($customer);
        
        $billingConfig = $customer->tenant?->billing_config ?? [];
        
        $customerData = [
            'id' => $customer->id,
            'name' => $customer->name,
            'nama_paket' => $customer->package?->name,
            'harga' => $customer->getEffectivePrice(),
            'tgl_daftar' => $customer->registration_date?->toDateString(),
            'jenis_bayar' => $customer->jenis_bayar ?? 'prabayar',
            'diskon_default' => 0, 
            'tambahan_layanan' => 0,
            'pakai_ppn' => $customer->pakai_ppn,
            'pakai_bhp' => $customer->pakai_bhp,
            'pakai_admin' => $customer->pakai_admin,
            'ppn_rate' => $billingConfig['ppn_rate'] ?? 11,
            'bhp_uso_rate' => $billingConfig['bhp_uso_rate'] ?? 1.25,
            'admin_fee' => $billingConfig['admin_fee'] ?? 2500,
        ];

        return response()->json([
            'status' => 'success',
            'customer' => $customerData,
            'paid_months' => $paidMonths,
        ]);
    }

    public function storeSingle(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'bulan_bayar' => 'required|array|min:1',
            'bulan_bayar.*' => 'date_format:Y-m',
            'tgl_bayar' => 'required|date',
            'metode' => 'required|string',
            'diskon' => 'nullable|integer|min:0',
            'paid_amount' => 'nullable|numeric|min:0', // Untuk partial payment
            'keterangan' => 'nullable|string|max:500',
        ]);

        $customer = Customer::findOrFail($data['customer_id']);
        if ($customer->tenant_id !== $request->user()->tenant_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        try {
            $result = $this->paymentService->processSinglePayment($customer, $data, $request->user());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function storeBatch(Request $request)
    {
        $data = $request->validate([
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
            'sampai_bulan' => 'nullable|date_format:Y-m',
            'tgl_bayar' => 'required|date',
            'metode' => 'required|string',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $count = $this->paymentService->processBatchPayment($data['customer_ids'], $data, $request->user());
            return response()->json([
                'status' => 'success',
                'message' => "{$count} pelanggan berhasil dibayar lunas.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses batch payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getWaTemplate(Request $request, Customer $customer)
    {
        if ($customer->tenant_id !== $request->user()->tenant_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $tenant = $customer->tenant;
        // Gunakan DB::table untuk mengambil setting
        $settings = \Illuminate\Support\Facades\DB::table('tenant_settings')->where('tenant_id', $tenant->id)->first();
        
        $template = $settings->template_wa_tagihan ?? 'Yth {nama}, tagihan Anda sebesar {nominal} untuk periode {bulan} telah terbit. Harap segera melakukan pembayaran. Terima kasih.';

        // Hitung total tagihan belum lunas
        $paidMonths = $this->paymentService->getPaymentMonths($customer);
        $totalTunggakan = 0;
        $bulanBelumLunas = [];
        $now = now()->format('Y-m');
        
        $billingConfig = $tenant->billing_config ?? [];
        $ppnRate = $billingConfig['ppn_rate'] ?? 11;
        $bhpRate = $billingConfig['bhp_uso_rate'] ?? 1.25;
        $adminFee = $billingConfig['admin_fee'] ?? 2500;
        
        foreach ($paidMonths as $ym => $info) {
            if (!$info['lunas'] && $ym <= $now) {
                $base = $info['remaining'];
                // Kurangi diskon (default 0 jika tidak ada)
                $diskon = $customer->diskon ?? 0;
                $afterDiskon = max(0, $base - $diskon);
                
                $ppn = $customer->pakai_ppn ? round($afterDiskon * ((float) $ppnRate / 100)) : 0;
                $bhp = $customer->pakai_bhp ? round($afterDiskon * ((float) $bhpRate / 100)) : 0;
                $admin = $customer->pakai_admin ? round($afterDiskon * ((float) $adminFee / 100)) : 0;
                
                $totalTunggakan += ($afterDiskon + $ppn + $bhp + $admin);
                $bulanBelumLunas[] = \Carbon\Carbon::parse($ym . '-01')->translatedFormat('F Y');
            }
        }

        $namaBulan = implode(', ', $bulanBelumLunas) ?: 'Bulan ini';

        // Replace placeholders
        $text = str_replace(
            ['{nama}', '{nominal}', '{bulan}', '{tgl_daftar}', '{paket}', '{jatuh_tempo}'],
            [
                $customer->name,
                'Rp ' . number_format($totalTunggakan, 0, ',', '.'),
                $namaBulan,
                $customer->registration_date ? $customer->registration_date->translatedFormat('d M Y') : '-',
                $customer->package?->name ?? '-',
                $customer->billing_date . ' setiap bulannya'
            ],
            $template
        );

        return response()->json([
            'status' => 'success',
            'phone' => $customer->phone,
            'message' => $text,
            'total_tunggakan' => $totalTunggakan,
            'bulan' => $namaBulan
        ]);
    }
}
