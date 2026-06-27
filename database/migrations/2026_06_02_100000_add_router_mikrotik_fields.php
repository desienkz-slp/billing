<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('routers', function (Blueprint $EUOFn) { goto Pz5el; sNM78: $EUOFn->boolean('auto_backup')->default(false)->after('use_ssl'); goto dI85p; dI85p: $EUOFn->boolean('use_radius')->default(false)->after('auto_backup'); goto cxWk0; cxWk0: $EUOFn->text('radius_secret')->nullable()->after('use_radius'); goto a3qam; Pz5el: $EUOFn->integer('ftp_port')->default(21)->after('port'); goto sNM78; a3qam: }); } public function down(): void { Schema::table('routers', function (Blueprint $EUOFn) { $EUOFn->dropColumn(['ftp_port', 'auto_backup', 'use_radius', 'radius_secret']); }); } };
