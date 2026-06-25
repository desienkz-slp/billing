<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('customers', function (Blueprint $VPdFm) { if (!Schema::hasColumn('customers', 'pakai_bhp')) { $VPdFm->boolean('pakai_bhp')->default(false)->after('pakai_ppn'); } if (!Schema::hasColumn('customers', 'pakai_admin')) { $VPdFm->boolean('pakai_admin')->default(false)->after('pakai_bhp'); } }); } public function down(): void { Schema::table('customers', function (Blueprint $VPdFm) { $VPdFm->dropColumn(['pakai_bhp', 'pakai_admin']); }); } };