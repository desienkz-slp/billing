<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('roles', function (Blueprint $EUOFn) { $EUOFn->boolean('can_access_desktop')->default(true)->after('description'); $EUOFn->boolean('can_access_mobile')->default(true)->after('can_access_desktop'); }); } public function down(): void { Schema::table('roles', function (Blueprint $EUOFn) { $EUOFn->dropColumn(['can_access_desktop', 'can_access_mobile']); }); } };
