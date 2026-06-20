<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->jsonb('company_profile')->nullable();
            $table->jsonb('billing_config')->nullable();
            $table->string('timezone', 50)->default('Asia/Jakarta');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('slug');
            $table->index('is_active');
        });

        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('plan', 50);
            $table->string('license_key')->unique();
            $table->integer('max_customers')->default(50);
            $table->jsonb('features')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->timestamp('grace_until')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_subscriptions');
        Schema::dropIfExists('tenants');
    }
};
