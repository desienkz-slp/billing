<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:31              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Traits; use App\Models\Tenant; use App\Scopes\TenantScope; use Illuminate\Database\Eloquent\Relations\BelongsTo; trait BelongsToTenant { public static function bootBelongsToTenant(): void { static::addGlobalScope(new TenantScope()); static::creating(function ($iOznW) { if (!$iOznW->tenant_id && app()->has('current_tenant')) { $jywU0 = app('current_tenant'); if ($jywU0) { $iOznW->tenant_id = $jywU0->id; } } }); } public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); } public function scopeWithoutTenant($SUGw0) { return $SUGw0->withoutGlobalScope(TenantScope::class); } }