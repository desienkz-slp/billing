<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:35              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('users', function (Blueprint $R6GoX) { $R6GoX->boolean('is_default_sales')->default(false)->after('is_active'); }); } public function down(): void { Schema::table('users', function (Blueprint $R6GoX) { $R6GoX->dropColumn('is_default_sales'); }); } };