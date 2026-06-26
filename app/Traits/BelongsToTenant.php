<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:33              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Traits; use App\Models\Tenant; use App\Scopes\TenantScope; use Illuminate\Database\Eloquent\Relations\BelongsTo; trait BelongsToTenant { public static function bootBelongsToTenant(): void { static::addGlobalScope(new TenantScope()); static::creating(function ($a8GXh) { if (!$a8GXh->tenant_id && app()->has('current_tenant')) { $EUm2s = app('current_tenant'); if ($EUm2s) { $a8GXh->tenant_id = $EUm2s->id; } } }); } public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); } public function scopeWithoutTenant($OpYw4) { return $OpYw4->withoutGlobalScope(TenantScope::class); } }
