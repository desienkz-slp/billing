<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TenantSubscription extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['features' => 'array', 'starts_at' => 'datetime', 'expires_at' => 'datetime', 'grace_until' => 'datetime'];
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function isActive(): bool { return in_array($this->status, ['active', 'grace']) && $this->expires_at->isFuture(); }
}
