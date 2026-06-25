<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:24              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response; class SystemAdmin { public function handle(Request $NtbRg, Closure $NWUQt): Response { goto NtQQ_; NtQQ_: $xKnNk = $NtbRg->user(); goto s1MGA; Lbcmi: return $NWUQt($NtbRg); goto HRJr4; s1MGA: if (!$xKnNk || !$xKnNk->is_system_admin) { if ($NtbRg->expectsJson()) { return response()->json(['message' => 'Akses ditolak. Hanya System Admin.'], 403); } abort(403, 'Akses ditolak. Hanya System Admin.'); } goto Lbcmi; HRJr4: } }