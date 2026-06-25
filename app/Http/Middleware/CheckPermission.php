<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:51              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response; class CheckPermission { public function handle(Request $u9qhv, Closure $N_zj8, string ...$IZGZZ): Response { goto e4F06; i2Gek: if (!$Ie2cF) { abort(403, 'Akses ditolak.'); } goto QMcI2; FCXAB: $NQd6b = $Ie2cF->role; goto WRsMn; ypT6r: abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.'); goto M53v4; QMcI2: if ($Ie2cF->is_system_admin) { return $N_zj8($u9qhv); } goto FCXAB; oknoF: foreach ($IZGZZ as $VS3ZQ) { $KL5tn = trim($VS3ZQ); if ($NQd6b->hasPermission($KL5tn)) { return $N_zj8($u9qhv); } } goto ypT6r; WRsMn: if (!$NQd6b) { abort(403, 'Role tidak ditemukan. Hubungi administrator.'); } goto oknoF; e4F06: $Ie2cF = $u9qhv->user(); goto i2Gek; M53v4: } }
