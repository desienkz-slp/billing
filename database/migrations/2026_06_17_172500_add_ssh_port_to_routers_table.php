<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('routers', function (Blueprint $aKYtW) { if (!Schema::hasColumn('routers', 'ssh_port')) { $aKYtW->integer('ssh_port')->default(22)->after('port'); } }); } public function down(): void { Schema::table('routers', function (Blueprint $aKYtW) { if (Schema::hasColumn('routers', 'ssh_port')) { $aKYtW->dropColumn('ssh_port'); } }); } };