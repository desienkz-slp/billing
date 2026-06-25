<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:34              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('customers', function (Blueprint $SxFR7) { goto T5Axz; xPwT1: $SxFR7->integer('max_tunggakan')->default(1)->after('auto_isolir'); goto F5Xbb; F5Xbb: $SxFR7->boolean('pakai_ppn')->default(false)->after('max_tunggakan'); goto JX2RV; JX2RV: $SxFR7->boolean('auto_wa_tagihan')->default(true)->after('pakai_ppn'); goto ucIZM; T5Axz: $SxFR7->boolean('auto_isolir')->default(true)->after('status'); goto xPwT1; ucIZM: }); } public function down(): void { Schema::table('customers', function (Blueprint $SxFR7) { $SxFR7->dropColumn(['auto_isolir', 'max_tunggakan', 'pakai_ppn', 'auto_wa_tagihan']); }); } };