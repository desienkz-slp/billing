<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'sales_id')) {
                $table->unsignedBigInteger('sales_id')->nullable()->after('customer_id');
                $table->integer('sales_fee_amount')->default(0)->after('sales_id');
                $table->integer('collector_fee_amount')->default(0)->after('collected_by');
                
                $table->foreign('sales_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['sales_id']);
            $table->dropColumn(['sales_id', 'sales_fee_amount', 'collector_fee_amount']);
        });
    }
};
