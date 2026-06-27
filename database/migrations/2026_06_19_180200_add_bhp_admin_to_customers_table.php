<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('customers', function (Blueprint $EUOFn) { if (!Schema::hasColumn('customers', 'pakai_bhp')) { $EUOFn->boolean('pakai_bhp')->default(false)->after('pakai_ppn'); } if (!Schema::hasColumn('customers', 'pakai_admin')) { $EUOFn->boolean('pakai_admin')->default(false)->after('pakai_bhp'); } }); } public function down(): void { Schema::table('customers', function (Blueprint $EUOFn) { $EUOFn->dropColumn(['pakai_bhp', 'pakai_admin']); }); } };
