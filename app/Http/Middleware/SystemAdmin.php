<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:35              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response; class SystemAdmin { public function handle(Request $CaBx1, Closure $CEf0e): Response { goto SAseP; CrKKq: return $CEf0e($CaBx1); goto GUOAx; gyuM7: if (!$mNbko || !$mNbko->is_system_admin) { if ($CaBx1->expectsJson()) { return response()->json(['message' => 'Akses ditolak. Hanya System Admin.'], 403); } abort(403, 'Akses ditolak. Hanya System Admin.'); } goto CrKKq; SAseP: $mNbko = $CaBx1->user(); goto gyuM7; GUOAx: } }
