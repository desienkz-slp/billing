<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:35              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Resources; use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource; class PaymentResource extends JsonResource { public function toArray(Request $CaBx1): array { return ['id' => $this->id, 'uuid' => $this->uuid, 'customer' => new CustomerResource($this->whenLoaded('customer')), 'invoice' => $this->whenLoaded('invoice', fn() => ['id' => $this->invoice->id, 'uuid' => $this->invoice->uuid, 'invoice_number' => $this->invoice->invoice_number]), 'receipt_number' => $this->receipt_number, 'receipt_group' => $this->receipt_group, 'payment_date' => $this->payment_date?->toDateString(), 'period' => $this->period, 'amount' => $this->amount, 'ppn_amount' => $this->ppn_amount, 'discount' => $this->discount, 'paid_amount' => $this->paid_amount, 'paid_amount_formatted' => 'Rp ' . number_format($this->paid_amount, 0, ',', '.'), 'payment_method' => $this->payment_method, 'collector' => $this->whenLoaded('collector', fn() => ['id' => $this->collector->id, 'name' => $this->collector->name]), 'notes' => $this->notes, 'status' => $this->status, 'cancelled_at' => $this->cancelled_at, 'created_at' => $this->created_at?->toIso8601String()]; } }
