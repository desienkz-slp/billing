<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('roles', function (Blueprint $EN32e) { $EN32e->boolean('can_send_wa_invoice')->default(false)->after('can_process_payment'); }); } public function down(): void { Schema::table('roles', function (Blueprint $EN32e) { $EN32e->dropColumn('can_send_wa_invoice'); }); } };