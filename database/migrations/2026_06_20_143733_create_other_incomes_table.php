<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('other_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('category', 50); // e.g. instalasi, perangkat, denda, lainnya
            $table->integer('amount');
            $table->date('income_date');
            $table->string('payment_method', 30)->default('cash');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['tenant_id', 'income_date']);
            $table->index(['tenant_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_incomes');
    }
};
