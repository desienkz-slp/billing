<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('customers', function (Blueprint $EUOFn) { goto v3D7m; dil4p: $EUOFn->decimal('diskon', 15, 2)->nullable()->after('custom_price'); goto GEdfx; w3Knd: $EUOFn->text('deskripsi_layanan')->nullable()->after('tambahan_layanan'); goto wic2z; v3D7m: $EUOFn->integer('tgl_isolir')->nullable()->after('billing_date'); goto dil4p; GEdfx: $EUOFn->string('tambahan_layanan')->nullable()->after('notes'); goto w3Knd; wic2z: }); } public function down(): void { Schema::table('customers', function (Blueprint $EUOFn) { $EUOFn->dropColumn(['tgl_isolir', 'diskon', 'tambahan_layanan', 'deskripsi_layanan']); }); } };
