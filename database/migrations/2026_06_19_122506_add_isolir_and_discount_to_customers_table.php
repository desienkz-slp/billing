<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('customers', function (Blueprint $jvlJM) { goto AbxzH; OLWd4: $jvlJM->text('deskripsi_layanan')->nullable()->after('tambahan_layanan'); goto nnWXW; z_qi7: $jvlJM->decimal('diskon', 15, 2)->nullable()->after('custom_price'); goto GYOft; AbxzH: $jvlJM->integer('tgl_isolir')->nullable()->after('billing_date'); goto z_qi7; GYOft: $jvlJM->string('tambahan_layanan')->nullable()->after('notes'); goto OLWd4; nnWXW: }); } public function down(): void { Schema::table('customers', function (Blueprint $jvlJM) { $jvlJM->dropColumn(['tgl_isolir', 'diskon', 'tambahan_layanan', 'deskripsi_layanan']); }); } };