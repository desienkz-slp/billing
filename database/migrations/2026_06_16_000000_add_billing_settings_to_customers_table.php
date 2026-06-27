<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('customers', function (Blueprint $EUOFn) { goto ocJ0K; ANaaN: $EUOFn->boolean('pakai_ppn')->default(false)->after('max_tunggakan'); goto OhtgF; MmMpN: $EUOFn->integer('max_tunggakan')->default(1)->after('auto_isolir'); goto ANaaN; ocJ0K: $EUOFn->boolean('auto_isolir')->default(true)->after('status'); goto MmMpN; OhtgF: $EUOFn->boolean('auto_wa_tagihan')->default(true)->after('pakai_ppn'); goto Y1Nd2; Y1Nd2: }); } public function down(): void { Schema::table('customers', function (Blueprint $EUOFn) { $EUOFn->dropColumn(['auto_isolir', 'max_tunggakan', 'pakai_ppn', 'auto_wa_tagihan']); }); } };
