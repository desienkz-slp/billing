<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('payments', function (Blueprint $EUOFn) { if (!Schema::hasColumn('payments', 'sales_id')) { goto mtntu; mtntu: $EUOFn->unsignedBigInteger('sales_id')->nullable()->after('customer_id'); goto Z5Xk0; Z5Xk0: $EUOFn->integer('sales_fee_amount')->default(0)->after('sales_id'); goto xbdWP; BwwBy: $EUOFn->foreign('sales_id')->references('id')->on('users')->nullOnDelete(); goto K4wB4; xbdWP: $EUOFn->integer('collector_fee_amount')->default(0)->after('collected_by'); goto BwwBy; K4wB4: } }); } public function down(): void { Schema::table('payments', function (Blueprint $EUOFn) { $EUOFn->dropForeign(['sales_id']); $EUOFn->dropColumn(['sales_id', 'sales_fee_amount', 'collector_fee_amount']); }); } };
