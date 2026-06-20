<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use BelongsToTenant;
    protected $guarded = ['id'];
    protected $casts = ['invoice_date' => 'date'];

    protected static function booted(): void
    {
        static::creating(fn(Invoice $i) => $i->uuid = $i->uuid ?: (string) Str::uuid());
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }

    public function isPaid(): bool { return $this->status === 'paid'; }
    public function getRemainingAmount(): int { return max(0, $this->total_amount - $this->payments()->where('status', 'paid')->sum('paid_amount')); }
}
