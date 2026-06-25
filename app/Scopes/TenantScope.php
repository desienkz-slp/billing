<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Scopes; use Illuminate\Database\Eloquent\Builder; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Scope; class TenantScope implements Scope { public function apply(Builder $nyoZv, Model $PCk25): void { goto OLxr1; EaWvR: $sPiAm = app('current_tenant'); goto L2Wae; L2Wae: if ($sPiAm) { $nyoZv->where($PCk25->getTable() . '.tenant_id', $sPiAm->id); } goto Y0pXi; OLxr1: if (!app()->bound('current_tenant')) { return; } goto EaWvR; Y0pXi: } }
