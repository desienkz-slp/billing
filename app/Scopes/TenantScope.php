<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Scopes; use Illuminate\Database\Eloquent\Builder; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Scope; class TenantScope implements Scope { public function apply(Builder $Z4w1V, Model $KyPZF): void { goto YgrS3; YgrS3: if (!app()->bound('current_tenant')) { return; } goto bVAbg; bVAbg: $VXSyW = app('current_tenant'); goto Y6wei; Y6wei: if ($VXSyW) { $Z4w1V->where($KyPZF->getTable() . '.tenant_id', $VXSyW->id); } goto m9OBO; m9OBO: } }
