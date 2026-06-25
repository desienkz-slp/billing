<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:30              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Scopes; use Illuminate\Database\Eloquent\Builder; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Scope; class TenantScope implements Scope { public function apply(Builder $fNjq9, Model $awySJ): void { goto sVlbO; wSZeG: $D6n1m = app('current_tenant'); goto wbYCx; sVlbO: if (!app()->bound('current_tenant')) { return; } goto wSZeG; wbYCx: if ($D6n1m) { $fNjq9->where($awySJ->getTable() . '.tenant_id', $D6n1m->id); } goto LbFe4; LbFe4: } }