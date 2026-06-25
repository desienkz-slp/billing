<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:34              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('users', function (Blueprint $pHC4L) { if (!Schema::hasColumn('users', 'fee_persen')) { $pHC4L->decimal('fee_persen', 5, 2)->default(0)->after('is_active'); } if (!Schema::hasColumn('users', 'fee_fix')) { $pHC4L->integer('fee_fix')->default(0)->after('fee_persen'); } }); } public function down(): void { Schema::table('users', function (Blueprint $pHC4L) { $pHC4L->dropColumn(['fee_persen', 'fee_fix']); }); } };