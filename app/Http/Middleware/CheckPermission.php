<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:35              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response; class CheckPermission { public function handle(Request $CaBx1, Closure $CEf0e, string ...$FUVYk): Response { goto OkJN1; T99mk: if (!$mNbko) { abort(403, 'Akses ditolak.'); } goto HPmSg; OkJN1: $mNbko = $CaBx1->user(); goto T99mk; TfnS8: if (!$AlXrH) { abort(403, 'Role tidak ditemukan. Hubungi administrator.'); } goto ppk1D; ppk1D: foreach ($FUVYk as $hRtll) { $rFPgb = trim($hRtll); if (str_contains($rFPgb, '.')) { if ($mNbko->hasCapability($rFPgb)) { return $CEf0e($CaBx1); } } else if ($AlXrH->hasPermission($rFPgb)) { return $CEf0e($CaBx1); } } goto hUDu0; hUDu0: abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.'); goto aveUn; HPmSg: if ($mNbko->is_system_admin) { return $CEf0e($CaBx1); } goto UyyUI; UyyUI: $AlXrH = $mNbko->role; goto TfnS8; aveUn: } }
