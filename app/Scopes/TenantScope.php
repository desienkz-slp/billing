<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:32              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Scopes; use Illuminate\Database\Eloquent\Builder; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Scope; class TenantScope implements Scope { public function apply(Builder $Xyk1i, Model $a8GXh): void { goto FEH5l; Ppw0G: if ($EUm2s) { $Xyk1i->where($a8GXh->getTable() . '.tenant_id', $EUm2s->id); } goto rH6sR; ZVCzf: $EUm2s = app('current_tenant'); goto Ppw0G; FEH5l: if (!app()->bound('current_tenant')) { return; } goto ZVCzf; rH6sR: } }
