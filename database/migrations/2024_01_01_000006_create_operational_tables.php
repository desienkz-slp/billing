<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expenses / Pengeluaran
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->date('expense_date');
            $table->string('category', 100)->nullable();
            $table->text('description');
            $table->integer('amount');
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['tenant_id', 'expense_date']);
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // Deposits / Setoran sales
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('sales_id');
            $table->unsignedBigInteger('received_by')->nullable();
            $table->date('deposit_date');
            $table->integer('amount');
            $table->string('method', 30)->default('cash');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'sales_id']);
            $table->index(['tenant_id', 'deposit_date']);
            $table->foreign('sales_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
        });

        // Deposit ledger entries
        Schema::create('deposit_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('sales_id');
            $table->string('type', 20);                   // credit, debit
            $table->integer('amount');
            $table->integer('balance_after');
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'sales_id']);
            $table->foreign('sales_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // Isolir jobs queue
        Schema::create('isolir_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('action', 20);                  // isolir, unisolir
            $table->string('status', 20)->default('pending');
            $table->integer('retry_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        // Isolir logs (history)
        Schema::create('isolir_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('action', 20);
            $table->string('method', 30)->nullable();      // manual, auto, payment_release
            $table->unsignedBigInteger('executed_by')->nullable();
            $table->boolean('success')->default(true);
            $table->text('details')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'customer_id']);
            $table->foreign('executed_by')->references('id')->on('users')->nullOnDelete();
        });

        // Audit logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action', 50);
            $table->string('model_type', 100)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->jsonb('old_values')->nullable();
            $table->jsonb('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'created_at']);
            $table->index(['tenant_id', 'action']);
            $table->index(['model_type', 'model_id']);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // Devices (FCM tokens for push notifications)
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('device_token')->unique();
            $table->string('platform', 20)->default('android');
            $table->string('device_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'user_id']);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // PPPoE tables
        Schema::create('pppoe_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('router_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('rate_limit')->nullable();
            $table->string('local_address')->nullable();
            $table->string('remote_address')->nullable();
            $table->jsonb('mikrotik_data')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'router_id']);
        });

        Schema::create('pppoe_secrets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('router_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->string('password_plain')->nullable();
            $table->string('profile', 100)->nullable();
            $table->string('service', 20)->default('pppoe');
            $table->boolean('disabled')->default(false);
            $table->string('comment')->nullable();
            $table->jsonb('mikrotik_data')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'router_id']);
            $table->index(['tenant_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pppoe_secrets');
        Schema::dropIfExists('pppoe_profiles');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('isolir_logs');
        Schema::dropIfExists('isolir_jobs');
        Schema::dropIfExists('deposit_ledger_entries');
        Schema::dropIfExists('deposits');
        Schema::dropIfExists('expenses');
    }
};
