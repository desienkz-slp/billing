<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:37              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Traits; use App\Models\Tenant; use App\Scopes\TenantScope; use Illuminate\Database\Eloquent\Relations\BelongsTo; trait BelongsToTenant { public static function bootBelongsToTenant(): void { static::addGlobalScope(new TenantScope()); static::creating(function ($KyPZF) { if (!$KyPZF->tenant_id && app()->has('current_tenant')) { $VXSyW = app('current_tenant'); if ($VXSyW) { $KyPZF->tenant_id = $VXSyW->id; } } }); } public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); } public function scopeWithoutTenant($Oa5s4) { return $Oa5s4->withoutGlobalScope(TenantScope::class); } }
