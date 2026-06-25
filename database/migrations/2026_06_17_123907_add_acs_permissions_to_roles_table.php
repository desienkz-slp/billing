<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('roles', function (Blueprint $J0sTJ) { $J0sTJ->boolean('can_view_acs')->default(false)->after('can_view_radius'); $J0sTJ->boolean('can_manage_acs')->default(false)->after('can_manage_olt'); }); } public function down(): void { Schema::table('roles', function (Blueprint $J0sTJ) { $J0sTJ->dropColumn(['can_view_acs', 'can_manage_acs']); }); } };