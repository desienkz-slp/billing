<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:51              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response; class SystemAdmin { public function handle(Request $u9qhv, Closure $N_zj8): Response { goto aVfbv; ePfSe: if (!$Ie2cF || !$Ie2cF->is_system_admin) { if ($u9qhv->expectsJson()) { return response()->json(['message' => 'Akses ditolak. Hanya System Admin.'], 403); } abort(403, 'Akses ditolak. Hanya System Admin.'); } goto Ry7IJ; aVfbv: $Ie2cF = $u9qhv->user(); goto ePfSe; Ry7IJ: return $N_zj8($u9qhv); goto kk3iG; kk3iG: } }
