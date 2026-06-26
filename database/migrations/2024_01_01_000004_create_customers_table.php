<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('router_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('server_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('odp_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('sales_id')->nullable();

            $table->string('customer_id_display', 50)->nullable();
            $table->string('name');
            $table->string('username', 100)->nullable();
            $table->text('password_pppoe')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('nik', 20)->nullable();
            $table->date('registration_date')->nullable();
            $table->integer('billing_date')->default(1);
            $table->integer('custom_price')->nullable();
            $table->string('odp_port', 20)->nullable();

            // Status
            $table->string('status', 30)->default('active');
            $table->boolean('is_isolated')->default(false);
            $table->date('isolated_since')->nullable();
            $table->boolean('is_on_leave')->default(false);
            $table->date('leave_start')->nullable();
            $table->date('leave_end')->nullable();

            // Metadata
            $table->text('notes')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'area_id']);
            $table->index(['tenant_id', 'package_id']);
            $table->index(['tenant_id', 'is_isolated']);
            $table->unique(['tenant_id', 'username']);

            $table->foreign('sales_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('customer_coordinates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();

            $table->unique('customer_id');
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_coordinates');
        Schema::dropIfExists('customers');
    }
};
