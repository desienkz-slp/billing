<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Invoices / Tagihan
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number', 50);
            $table->string('receipt_group', 50)->nullable();
            $table->date('invoice_date');
            $table->string('period', 7);                   // YYYY-MM
            $table->integer('amount');
            $table->integer('ppn_amount')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('total_amount');
            $table->string('status', 20)->default('unpaid'); // unpaid, paid, partial, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'customer_id']);
            $table->index(['tenant_id', 'period']);
            $table->index(['tenant_id', 'status']);
            $table->unique(['tenant_id', 'invoice_number']);
        });

        // Payments / Pembayaran
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('collected_by')->nullable();

            $table->string('receipt_number', 50)->nullable();
            $table->string('receipt_group', 50)->nullable();
            $table->date('payment_date');
            $table->string('period', 7);                   // YYYY-MM
            $table->integer('amount');
            $table->integer('ppn_amount')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('paid_amount');
            $table->string('payment_method', 30)->default('cash');
            $table->string('status', 20)->default('paid');  // paid, cancelled
            $table->text('notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'customer_id']);
            $table->index(['tenant_id', 'payment_date']);
            $table->index(['tenant_id', 'period']);
            $table->index(['tenant_id', 'receipt_group']);
            $table->index(['tenant_id', 'collected_by']);

            $table->foreign('collected_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();
        });

        // Monthly Balances / Ledger piutang per bulan per pelanggan
        Schema::create('monthly_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('period', 7);                   // YYYY-MM
            $table->integer('charge_amount')->default(0);   // tagihan bulan ini
            $table->integer('paid_amount')->default(0);     // sudah dibayar
            $table->integer('balance')->default(0);         // sisa (charge - paid)
            $table->string('status', 20)->default('unpaid'); // unpaid, paid, partial
            $table->timestamps();

            $table->unique(['tenant_id', 'customer_id', 'period']);
            $table->index(['tenant_id', 'period', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_balances');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
