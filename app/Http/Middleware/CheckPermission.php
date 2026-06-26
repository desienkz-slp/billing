<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 20:40:51              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response; class CheckPermission { public function handle(Request $u9qhv, Closure $N_zj8, string ...$IZGZZ): Response { goto JusGH; sqKDm: foreach ($IZGZZ as $VS3ZQ) { $KL5tn = trim($VS3ZQ); if (str_contains($KL5tn, '.')) { if ($Ie2cF->hasCapability($KL5tn)) { return $N_zj8($u9qhv); } } else if ($NQd6b->hasPermission($KL5tn)) { return $N_zj8($u9qhv); } } goto s57t2; s57t2: abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.'); goto qf2P_; Ubfk4: $NQd6b = $Ie2cF->role; goto xKdBw; ToU7B: if ($Ie2cF->is_system_admin) { return $N_zj8($u9qhv); } goto Ubfk4; TS3pi: if (!$Ie2cF) { abort(403, 'Akses ditolak.'); } goto ToU7B; xKdBw: if (!$NQd6b) { abort(403, 'Role tidak ditemukan. Hubungi administrator.'); } goto sqKDm; JusGH: $Ie2cF = $u9qhv->user(); goto TS3pi; qf2P_: } }
