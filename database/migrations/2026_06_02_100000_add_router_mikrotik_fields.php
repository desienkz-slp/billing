<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:33              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('routers', function (Blueprint $V2Hcu) { goto OfUJc; OfUJc: $V2Hcu->integer('ftp_port')->default(21)->after('port'); goto wUr90; wUr90: $V2Hcu->boolean('auto_backup')->default(false)->after('use_ssl'); goto tsgRK; tsgRK: $V2Hcu->boolean('use_radius')->default(false)->after('auto_backup'); goto qTlgY; qTlgY: $V2Hcu->text('radius_secret')->nullable()->after('use_radius'); goto b3UKZ; b3UKZ: }); } public function down(): void { Schema::table('routers', function (Blueprint $V2Hcu) { $V2Hcu->dropColumn(['ftp_port', 'auto_backup', 'use_radius', 'radius_secret']); }); } };