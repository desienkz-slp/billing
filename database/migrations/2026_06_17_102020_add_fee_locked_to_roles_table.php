<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:35              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('roles', function (Blueprint $J5i3f) { $J5i3f->boolean('fee_locked')->default(false)->after('fee_fix')->comment('Jika true, fee user tetap (tidak berubah) meskipun dilapor bayar oleh user lain'); }); } public function down(): void { Schema::table('roles', function (Blueprint $J5i3f) { $J5i3f->dropColumn('fee_locked'); }); } };