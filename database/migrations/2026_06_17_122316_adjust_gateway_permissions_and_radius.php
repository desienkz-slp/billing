<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:35              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('roles', function (Blueprint $eURbM) { $eURbM->renameColumn('can_access_radius', 'can_view_radius'); $eURbM->boolean('can_config_radius')->default(false)->after('can_manage_pppoe'); }); } public function down(): void { Schema::table('roles', function (Blueprint $eURbM) { $eURbM->renameColumn('can_view_radius', 'can_access_radius'); $eURbM->dropColumn('can_config_radius'); }); } };