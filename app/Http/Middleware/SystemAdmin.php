<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:32              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response; class SystemAdmin { public function handle(Request $DVgdY, Closure $A9U5q): Response { goto UGgAo; UGgAo: $l8Pdr = $DVgdY->user(); goto rbpJh; Jrzpp: return $A9U5q($DVgdY); goto a5ycA; rbpJh: if (!$l8Pdr || !$l8Pdr->is_system_admin) { if ($DVgdY->expectsJson()) { return response()->json(['message' => 'Akses ditolak. Hanya System Admin.'], 403); } abort(403, 'Akses ditolak. Hanya System Admin.'); } goto Jrzpp; a5ycA: } }
