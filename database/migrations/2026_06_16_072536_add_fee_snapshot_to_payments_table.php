<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:35              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('payments', function (Blueprint $eWf7U) { if (!Schema::hasColumn('payments', 'sales_id')) { goto OCuBD; r3hsq: $eWf7U->integer('sales_fee_amount')->default(0)->after('sales_id'); goto YeD7L; tJrav: $eWf7U->foreign('sales_id')->references('id')->on('users')->nullOnDelete(); goto dlZMJ; OCuBD: $eWf7U->unsignedBigInteger('sales_id')->nullable()->after('customer_id'); goto r3hsq; YeD7L: $eWf7U->integer('collector_fee_amount')->default(0)->after('collected_by'); goto tJrav; dlZMJ: } }); } public function down(): void { Schema::table('payments', function (Blueprint $eWf7U) { $eWf7U->dropForeign(['sales_id']); $eWf7U->dropColumn(['sales_id', 'sales_fee_amount', 'collector_fee_amount']); }); } };