<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { if (!Schema::hasColumn('package_routers', 'isolir_profile')) { Schema::table('package_routers', function (Blueprint $EUOFn) { $EUOFn->string('isolir_profile', 100)->nullable()->after('pppoe_profile'); }); } } public function down(): void { Schema::table('package_routers', function (Blueprint $EUOFn) { $EUOFn->dropColumn('isolir_profile'); }); } };
