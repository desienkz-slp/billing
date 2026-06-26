<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:32              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response; class CheckPermission { public function handle(Request $DVgdY, Closure $A9U5q, string ...$hq6wU): Response { goto K1yRo; K1yRo: $l8Pdr = $DVgdY->user(); goto r49BS; b_78u: abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.'); goto yz7aP; U7CD_: foreach ($hq6wU as $MdN08) { $BqJsR = trim($MdN08); if (str_contains($BqJsR, '.')) { if ($l8Pdr->hasCapability($BqJsR)) { return $A9U5q($DVgdY); } } else if ($U0LQz->hasPermission($BqJsR)) { return $A9U5q($DVgdY); } } goto b_78u; gGhoI: if ($l8Pdr->is_system_admin) { return $A9U5q($DVgdY); } goto hj2KZ; hj2KZ: $U0LQz = $l8Pdr->role; goto rWF4l; rWF4l: if (!$U0LQz) { abort(403, 'Role tidak ditemukan. Hubungi administrator.'); } goto U7CD_; r49BS: if (!$l8Pdr) { abort(403, 'Akses ditolak.'); } goto gGhoI; yz7aP: } }
