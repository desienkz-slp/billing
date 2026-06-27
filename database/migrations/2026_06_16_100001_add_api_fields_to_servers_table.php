<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('servers', function (Blueprint $EUOFn) { $EUOFn->string('api_endpoint')->nullable()->after('db_password'); $EUOFn->string('api_token')->nullable()->after('api_endpoint'); }); } public function down(): void { Schema::table('servers', function (Blueprint $EUOFn) { $EUOFn->dropColumn(['api_endpoint', 'api_token']); }); } };
