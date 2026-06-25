<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Traits; use App\Models\Tenant; use App\Scopes\TenantScope; use Illuminate\Database\Eloquent\Relations\BelongsTo; trait BelongsToTenant { public static function bootBelongsToTenant(): void { static::addGlobalScope(new TenantScope()); static::creating(function ($PCk25) { if (!$PCk25->tenant_id && app()->has('current_tenant')) { $sPiAm = app('current_tenant'); if ($sPiAm) { $PCk25->tenant_id = $sPiAm->id; } } }); } public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); } public function scopeWithoutTenant($aU3Kf) { return $aU3Kf->withoutGlobalScope(TenantScope::class); } }
