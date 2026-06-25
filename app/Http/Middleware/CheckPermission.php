<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:24              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response; class CheckPermission { public function handle(Request $X8T8l, Closure $SXvjd, string ...$iIw1y): Response { goto sA9mB; xLsyQ: if (!$WhgYc) { abort(403, 'Akses ditolak.'); } goto rvhB7; sA9mB: $WhgYc = $X8T8l->user(); goto xLsyQ; HqeRg: abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.'); goto j6E0z; rvhB7: if ($WhgYc->is_system_admin) { return $SXvjd($X8T8l); } goto Oa7Iv; Oa7Iv: $U83aq = $WhgYc->role; goto yOhTT; BbIpI: foreach ($iIw1y as $pRl9C) { $kCIqz = trim($pRl9C); if ($U83aq->hasPermission($kCIqz)) { return $SXvjd($X8T8l); } } goto HqeRg; yOhTT: if (!$U83aq) { abort(403, 'Role tidak ditemukan. Hubungi administrator.'); } goto BbIpI; j6E0z: } }