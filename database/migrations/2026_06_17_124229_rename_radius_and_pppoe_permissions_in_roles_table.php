<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('roles', function (Blueprint $n1qVN) { $n1qVN->renameColumn('can_config_radius', 'can_manage_radius'); $n1qVN->renameColumn('can_manage_pppoe', 'can_manage_router'); }); } public function down(): void { Schema::table('roles', function (Blueprint $n1qVN) { $n1qVN->renameColumn('can_manage_radius', 'can_config_radius'); $n1qVN->renameColumn('can_manage_router', 'can_manage_pppoe'); }); } };