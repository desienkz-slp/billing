<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom ke customers
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('fee_persen', 5, 2)->default(0)->after('custom_price');
            $table->decimal('fee_fix', 12, 2)->default(0)->after('fee_persen');
            $table->unsignedBigInteger('created_by')->nullable()->after('metadata');
            $table->string('leave_reason')->nullable()->after('leave_end');

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['tenant_id', 'sales_id']);
        });

        // Tambah kolom ke users
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('fee_persen', 5, 2)->default(0)->after('is_active');
            $table->decimal('fee_fix', 12, 2)->default(0)->after('fee_persen');
            $table->decimal('deposit_balance', 12, 2)->default(0)->after('fee_fix');
            $table->string('deposit_periode', 7)->nullable()->after('deposit_balance');
            $table->jsonb('perms_view_area_ids')->nullable()->after('deposit_periode');
            $table->jsonb('perms_view_sales_ids')->nullable()->after('perms_view_area_ids');
        });

        // Tambah kolom ke payments (fee tracking)
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('fee_amount', 12, 2)->default(0)->after('paid_amount');
            $table->decimal('company_amount', 12, 2)->default(0)->after('fee_amount');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['fee_amount', 'company_amount']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fee_persen', 'fee_fix', 'deposit_balance', 'deposit_periode', 'perms_view_area_ids', 'perms_view_sales_ids']);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['fee_persen', 'fee_fix', 'created_by', 'leave_reason']);
        });
    }
};
