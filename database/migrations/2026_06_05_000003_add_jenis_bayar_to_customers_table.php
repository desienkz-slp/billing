<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:34              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('customers', function (Blueprint $DtH9T) { $DtH9T->string('jenis_bayar', 20)->default('prabayar')->after('status'); }); } public function down(): void { Schema::table('customers', function (Blueprint $DtH9T) { $DtH9T->dropColumn('jenis_bayar'); }); } };