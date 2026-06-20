<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * GET /api/v1/payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['customer', 'invoice'])
            ->where('status', '!=', 'cancelled');

        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
        }
        if ($period = $request->get('period')) {
            $query->where('period', $period);
        }
        if ($month = $request->get('month')) {
            $query->whereMonth('payment_date', $month);
        }
        if ($year = $request->get('year')) {
            $query->whereYear('payment_date', $year);
        }
        if ($method = $request->get('payment_method')) {
            $query->where('payment_method', $method);
        }

        $query->orderBy('created_at', 'desc');
        $perPage = min((int) $request->get('per_page', 25), 100);

        return PaymentResource::collection($query->paginate($perPage));
    }

    /**
     * POST /api/v1/payments
     * Multi-month payment support
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'periods' => 'required|array|min:1',
            'periods.*' => 'date_format:Y-m',
            'payment_date' => 'nullable|date',
            'payment_method' => 'required|in:cash,transfer,qris,deposit',
            'discount' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $customer = Customer::with('package')->findOrFail($request->customer_id);
        $user = $request->user();
        $payments = [];
        $receiptGroup = 'RG-' . now()->format('YmdHis') . '-' . Str::random(4);

        DB::beginTransaction();
        try {
            foreach ($request->periods as $period) {
                // Check duplicate
                $exists = Payment::where('customer_id', $customer->id)
                    ->where('period', $period)
                    ->where('status', 'paid')
                    ->exists();

                if ($exists) continue;

                $amount = $customer->getEffectivePrice();
                $discount = $request->discount ?? 0;
                $ppnRate = 0.11;
                $subtotal = $amount - $discount;
                $ppnAmount = (int) round($subtotal * $ppnRate);
                $totalAmount = $subtotal + $ppnAmount;
                $paymentDate = $request->payment_date ?? now()->toDateString();

                // Create invoice
                $invoice = Invoice::create([
                    'uuid' => (string) Str::uuid(),
                    'tenant_id' => $customer->tenant_id,
                    'customer_id' => $customer->id,
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'receipt_group' => $receiptGroup,
                    'invoice_date' => $paymentDate,
                    'period' => $period,
                    'amount' => $amount,
                    'ppn_amount' => $ppnAmount,
                    'discount' => $discount,
                    'total_amount' => $totalAmount,
                    'status' => 'paid',
                ]);

                // Create payment
                $payment = Payment::create([
                    'uuid' => (string) Str::uuid(),
                    'tenant_id' => $customer->tenant_id,
                    'customer_id' => $customer->id,
                    'invoice_id' => $invoice->id,
                    'collected_by' => $user->id,
                    'receipt_number' => $this->generateReceiptNumber(),
                    'receipt_group' => $receiptGroup,
                    'payment_date' => $paymentDate,
                    'period' => $period,
                    'amount' => $amount,
                    'ppn_amount' => $ppnAmount,
                    'discount' => $discount,
                    'paid_amount' => $totalAmount,
                    'payment_method' => $request->payment_method,
                    'status' => 'paid',
                    'notes' => $request->notes,
                ]);

                $payments[] = $payment;
            }

            // Auto-release isolir
            if ($customer->is_isolated && count($payments) > 0) {
                $customer->update(['is_isolated' => false, 'isolated_since' => null]);
            }

            DB::commit();

            return response()->json([
                'message' => count($payments) . ' pembayaran berhasil dicatat.',
                'data' => PaymentResource::collection(
                    collect($payments)->each(fn ($p) => $p->load('customer', 'invoice'))
                ),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menyimpan pembayaran.',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal error',
            ], 500);
        }
    }

    /**
     * GET /api/v1/payments/{payment}
     */
    public function show(Payment $payment): PaymentResource
    {
        $payment->load(['customer.package', 'invoice', 'collector']);
        return new PaymentResource($payment);
    }

    /**
     * POST /api/v1/payments/{payment}/cancel
     */
    public function cancel(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->status === 'cancelled') {
            return response()->json(['message' => 'Pembayaran sudah dibatalkan.'], 422);
        }

        $payment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => $request->user()->id,
        ]);

        if ($payment->invoice) {
            $payment->invoice->update(['status' => 'cancelled']);
        }

        return response()->json(['message' => 'Pembayaran berhasil dibatalkan.']);
    }

    /**
     * GET /api/v1/payments/summary
     */
    public function summary(Request $request): JsonResponse
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $base = Payment::where('status', 'paid')
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year);

        return response()->json([
            'data' => [
                'total_payments' => $base->count(),
                'total_amount' => $base->sum('paid_amount'),
                'total_formatted' => 'Rp ' . number_format($base->sum('paid_amount'), 0, ',', '.'),
                'by_method' => Payment::where('status', 'paid')
                    ->whereMonth('payment_date', $month)
                    ->whereYear('payment_date', $year)
                    ->selectRaw('payment_method, COUNT(*) as count, SUM(paid_amount) as total')
                    ->groupBy('payment_method')
                    ->get(),
                'month' => $month,
                'year' => $year,
            ],
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ym');
        $last = Invoice::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')->first();
        $seq = $last ? ((int) substr($last->invoice_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    private function generateReceiptNumber(): string
    {
        $prefix = 'RCP-' . now()->format('Ym');
        $last = Payment::where('receipt_number', 'like', $prefix . '%')
            ->orderBy('receipt_number', 'desc')->first();
        $seq = $last ? ((int) substr($last->receipt_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
