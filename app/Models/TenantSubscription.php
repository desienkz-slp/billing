<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; class TenantSubscription extends Model { protected $guarded = ['id']; protected $casts = ['features' => 'array', 'starts_at' => 'datetime', 'expires_at' => 'datetime', 'grace_until' => 'datetime']; public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); } public function isActive(): bool { return in_array($this->status, ['active', 'grace']) && $this->expires_at->isFuture(); } }
