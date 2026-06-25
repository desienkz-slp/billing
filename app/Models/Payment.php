<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:28              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; use Illuminate\Support\Str; class Payment extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['payment_date' => 'date', 'cancelled_at' => 'datetime']; protected static function booted(): void { static::creating(fn(Payment $Od1tn) => $Od1tn->uuid = $Od1tn->uuid ?: (string) Str::uuid()); } public function customer(): BelongsTo { return $this->belongsTo(Customer::class); } public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); } public function collector(): BelongsTo { return $this->belongsTo(User::class, 'collected_by'); } public function sales(): BelongsTo { return $this->belongsTo(User::class, 'sales_id'); } public function cancelledByUser(): BelongsTo { return $this->belongsTo(User::class, 'cancelled_by'); } public function isCancelled(): bool { return $this->status === 'cancelled'; } }