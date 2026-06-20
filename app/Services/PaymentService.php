<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\MonthlyBalance;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Get unpaid and future months for a customer.
     */
    public function getPaymentMonths(Customer $customer)
    {
        $tenantId = $customer->tenant_id;
        $now = now();
        $thisYm = $now->format('Y-m');
        
        $regDate = $customer->registration_date ? Carbon::parse($customer->registration_date) : $now->copy();
        
        if (($customer->jenis_bayar ?? 'prabayar') === 'pascabayar') {
            $regDate->addMonth(); // Pascabayar bayar bulan depannya
        }
        
        // Jangan terlalu jauh ke belakang (max 24 bulan)
        $minDate = $now->copy()->subMonths(24)->startOfMonth();
        if ($regDate->lessThan($minDate)) {
            $regDate = $minDate;
        }

        $startYm = $regDate->format('Y-m');
        $endYm = $now->copy()->addMonths(6)->format('Y-m'); // Tampilkan hingga 6 bulan ke depan

        // Ambil histori balance dari database
        $balances = MonthlyBalance::where('customer_id', $customer->id)
            ->whereBetween('period', [$startYm, $endYm])
            ->get()
            ->keyBy('period');

        $paidMonths = [];
        $current = Carbon::parse($startYm . '-01');
        $end = Carbon::parse($endYm . '-01');

        while ($current->lessThanOrEqualTo($end)) {
            $ym = $current->format('Y-m');
            $bal = $balances->get($ym);
            $effectivePrice = $customer->getEffectivePrice();

            if ($bal) {
                // Auto sync unpaid balance to current effective price
                if ($bal->status === 'unpaid' && $bal->charge_amount != $effectivePrice) {
                    $bal->charge_amount = $effectivePrice;
                    $bal->balance = $effectivePrice;
                    $bal->save();
                }

                $paidMonths[$ym] = [
                    'lunas' => $bal->status === 'paid',
                    'remaining' => max(0, $bal->balance),
                    'charge_amount' => $bal->charge_amount,
                ];
            } else {
                // Belum ada record di MonthlyBalance (berarti belum ditagihkan / belum lunas sama sekali)
                $paidMonths[$ym] = [
                    'lunas' => false,
                    'remaining' => $effectivePrice,
                    'charge_amount' => $effectivePrice,
                ];
            }
            $current->addMonth();
        }

        return $paidMonths;
    }

    /**
     * Process a payment for a single customer (supports partial).
     */
    public function processSinglePayment(Customer $customer, array $data, $user)
    {
        return DB::transaction(function () use ($customer, $data, $user) {
            $periods = collect($data['bulan_bayar'])->sort()->values()->toArray();
            $basePrice = $customer->getEffectivePrice();
            
            // Fetch configs
            $billingConfig = $customer->tenant?->billing_config ?? [];
            $ppnRate = $billingConfig['ppn_rate'] ?? 11;
            $bhpRate = $billingConfig['bhp_uso_rate'] ?? 1.25;
            $adminFee = $billingConfig['admin_fee'] ?? 2500;

            $pakaiPpn = $customer->pakai_ppn;
            $pakaiBhp = $customer->pakai_bhp;
            $pakaiAdmin = $customer->pakai_admin;

            $totalCharge = 0;
            $balancesToUpdate = [];

            foreach ($periods as $period) {
                // Ambil atau buat MonthlyBalance
                $balance = MonthlyBalance::firstOrCreate(
                    ['tenant_id' => $customer->tenant_id, 'customer_id' => $customer->id, 'period' => $period],
                    ['charge_amount' => $basePrice, 'paid_amount' => 0, 'balance' => $basePrice, 'status' => 'unpaid']
                );

                if ($balance->status === 'unpaid' && $balance->charge_amount != $basePrice) {
                    $balance->charge_amount = $basePrice;
                    $balance->balance = $basePrice;
                    $balance->save();
                }

                if ($balance->status === 'paid') {
                    continue; // Skip jika sudah lunas
                }

                $totalCharge += $balance->balance;
                $balancesToUpdate[] = $balance;
            }

            if (empty($balancesToUpdate)) {
                throw new \Exception('Tidak ada tagihan yang bisa dibayar.');
            }

            $discount = (int) ($data['diskon'] ?? 0);
            $amountAfterDiscount = max(0, $totalCharge - $discount);
            
            // Calculate Taxes and Fees
            $ppnAmount = $pakaiPpn ? (int) round($amountAfterDiscount * ((float) $ppnRate / 100)) : 0;
            $bhpAmount = $pakaiBhp ? (int) round($amountAfterDiscount * ((float) $bhpRate / 100)) : 0;
            $adminAmount = $pakaiAdmin ? (int) round($amountAfterDiscount * ((float) $adminFee / 100)) : 0;
            
            $grandTotal = $amountAfterDiscount + $ppnAmount + $bhpAmount + $adminAmount;

            // paid_amount adalah uang yang benar-benar diserahkan sekarang
            $paidAmount = isset($data['paid_amount']) ? (int) $data['paid_amount'] : $grandTotal;

            if ($paidAmount > $grandTotal) {
                $paidAmount = $grandTotal;
            }

            $isPartial = $paidAmount < $grandTotal;
            $remainingPayment = $paidAmount;

            // Distribute payment to balances
            // Pertama diskon mengurangi tagihan (kita sebar diskon rata atau langsung di total?)
            // Untuk simple: kita kurangi balance berurutan
            
            // Note: Pembayaran PPN diprioritaskan?
            // Cara termudah: kita anggap $paidAmount menutupi $totalCharge proporsional.
            // Karena ini sistem tagihan bulanan, kita kurangi dari balance bulan terlama.
            
            // Diskon hanya diberikan untuk tagihan, bukan PPN/BHP/Admin
            $remainingToCover = max(0, $paidAmount - $ppnAmount - $bhpAmount - $adminAmount); // bayar fee dulu
            if ($remainingToCover < 0) {
                $remainingToCover = 0;
            }

            foreach ($balancesToUpdate as $balance) {
                // Jika masih ada uang untuk bayar
                if ($remainingToCover > 0) {
                    $toPay = min($balance->balance, $remainingToCover);
                    $balance->paid_amount += $toPay;
                    $balance->balance -= $toPay;
                    $remainingToCover -= $toPay;
                }
                
                if ($balance->balance <= 0) {
                    $balance->status = 'paid';
                    $balance->balance = 0;
                } else {
                    $balance->status = $balance->paid_amount > 0 ? 'partial' : 'unpaid';
                }
                $balance->save();
            }

            $receiptGroup = 'RG-' . now()->format('YmdHis') . '-' . Str::random(4);
            $invPeriod = count($periods) > 1 ? reset($periods) : $periods[0];

            // Buat 1 Invoice untuk transaksi ini
            $invoice = Invoice::create([
                'uuid' => (string) Str::uuid(),
                'tenant_id' => $customer->tenant_id,
                'customer_id' => $customer->id,
                'invoice_number' => 'INV-' . now()->format('Ym') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT),
                'invoice_date' => $data['tgl_bayar'] ?? now()->toDateString(),
                'period' => $invPeriod,
                'amount' => $totalCharge,
                'ppn_amount' => $ppnAmount,
                'discount' => $discount,
                'total_amount' => $grandTotal,
                'status' => $isPartial ? 'partial' : 'paid',
                'receipt_group' => $receiptGroup,
                'notes' => implode(',', $periods), // Simpan list bulan di notes untuk histori
            ]);

            $salesFeeAmount = 0;
            $salesId = $customer->sales_id;
            if ($salesId && $customer->sales) {
                $sales = $customer->sales;
                if ($sales->fee_persen > 0) {
                    $salesFeeAmount += ($totalCharge * $sales->fee_persen) / 100;
                }
                if ($sales->fee_fix > 0) {
                    $salesFeeAmount += $sales->fee_fix;
                }
            }

            $collectorFeeAmount = 0;
            if ($user->fee_persen > 0) {
                $collectorFeeAmount += ($totalCharge * $user->fee_persen) / 100;
            }
            if ($user->fee_fix > 0) {
                $collectorFeeAmount += $user->fee_fix;
            }

            $payment = Payment::create([
                'uuid' => (string) Str::uuid(),
                'tenant_id' => $customer->tenant_id,
                'customer_id' => $customer->id,
                'invoice_id' => $invoice->id,
                'collected_by' => $user->id,
                'sales_id' => $salesId,
                'sales_fee_amount' => (int) $salesFeeAmount,
                'collector_fee_amount' => (int) $collectorFeeAmount,
                'receipt_group' => $receiptGroup,
                'receipt_number' => 'RCP-' . now()->format('Ym') . '-' . str_pad(Payment::count() + 1, 4, '0', STR_PAD_LEFT),
                'payment_date' => $data['tgl_bayar'] ?? now()->toDateString(),
                'period' => $invPeriod,
                'amount' => $totalCharge,
                'ppn_amount' => $ppnAmount,
                'discount' => $discount,
                'paid_amount' => $paidAmount,
                'payment_method' => $data['metode'] ?? 'cash',
                'status' => 'paid',
                'notes' => $data['keterangan'] ?? null,
            ]);

            if ($customer->is_isolated) {
                $customer->update(['is_isolated' => false, 'isolated_since' => null]);
                
                if ($customer->router_id && $customer->username) {
                    try {
                        $mikrotik = app(\App\Services\MikroTik\MikroTikService::class);
                        $packageRouter = \DB::table('package_routers')
                            ->where('package_id', $customer->package_id)
                            ->where('router_id', $customer->router_id)
                            ->first();
                        $normalProfile = $packageRouter?->pppoe_profile ?? 'default';
                        $mikrotik->changeSecretProfile($customer->router, $customer->username, $normalProfile);
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('Auto unisolir failed: ' . $e->getMessage());
                    }
                }
            }

            return [
                'status' => 'success',
                'id' => $payment->id,
                'jumlah' => $grandTotal,
                'subtotal' => $amountAfterDiscount,
                'diskon' => $discount,
                'ppn_amount' => $ppnAmount,
                'is_partial' => $isPartial,
                'message' => 'Pembayaran berhasil dicatat.',
            ];
        });
    }

    /**
     * Process batch payment for multiple customers (full payment only).
     */
    public function processBatchPayment(array $customerIds, array $data, $user)
    {
        $count = 0;
        foreach ($customerIds as $customerId) {
            $customer = Customer::find($customerId);
            if (!$customer) continue;

            $months = $this->getPaymentMonths($customer);
            $periodsToPay = [];
            $sampai = $data['sampai_bulan'] ?? null;

            foreach ($months as $ym => $info) {
                if (!$info['lunas']) {
                    if ($sampai && $ym > $sampai) continue;
                    $periodsToPay[] = $ym;
                }
            }

            if (!empty($periodsToPay)) {
                $paymentData = [
                    'bulan_bayar' => $periodsToPay,
                    'tgl_bayar' => $data['tgl_bayar'] ?? now()->toDateString(),
                    'metode' => $data['metode'] ?? 'cash',
                    'keterangan' => $data['keterangan'] ?? 'Batch Payment',
                    'diskon' => 0,
                ];
                $this->processSinglePayment($customer, $paymentData, $user);
                $count++;
            }
        }

        return $count;
    }
}
