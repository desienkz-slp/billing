<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Capability-based RBAC roles (~30 boolean permissions)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('description')->nullable();

            // Customer management
            $table->boolean('can_view_dashboard')->default(false);
            $table->boolean('can_input_customer')->default(false);
            $table->boolean('can_edit_customer')->default(false);
            $table->boolean('can_delete_customer')->default(false);
            $table->boolean('can_import_customer')->default(false);
            $table->boolean('can_export_customer')->default(false);

            // Payment & billing
            $table->boolean('can_process_payment')->default(false);
            $table->boolean('can_cancel_payment')->default(false);
            $table->boolean('can_view_payment_history')->default(false);

            // Isolir
            $table->boolean('can_manage_isolir')->default(false);
            $table->boolean('can_auto_isolir_config')->default(false);

            // Reports & finance
            $table->boolean('can_view_reports')->default(false);
            $table->boolean('can_view_finance')->default(false);
            $table->boolean('can_manage_expenses')->default(false);
            $table->boolean('can_manage_deposits')->default(false);
            $table->boolean('can_deduct_balance')->default(false);

            // Master data
            $table->boolean('can_manage_packages')->default(false);
            $table->boolean('can_manage_areas')->default(false);
            $table->boolean('can_manage_odp')->default(false);
            $table->boolean('can_manage_routers')->default(false);
            $table->boolean('can_manage_servers')->default(false);

            // Admin
            $table->boolean('can_manage_users')->default(false);
            $table->boolean('can_manage_roles')->default(false);
            $table->boolean('can_view_audit_logs')->default(false);
            $table->boolean('can_manage_config')->default(false);
            $table->boolean('can_backup_restore')->default(false);

            // Technical
            $table->boolean('can_access_mikrotik')->default(false);
            $table->boolean('can_access_radius')->default(false);
            $table->boolean('can_manage_pppoe')->default(false);
            $table->boolean('can_send_wa_blast')->default(false);

            // Map & monitor
            $table->boolean('can_view_map')->default(false);
            $table->boolean('can_view_monitor')->default(false);

            // Visibility scoping
            $table->jsonb('allowed_area_ids')->nullable();
            $table->jsonb('allowed_sales_ids')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'name']);
        });

        // Modify default users table to add tenant support
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->string('username', 100);
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['tenant_id', 'username']);
            $table->index(['tenant_id', 'is_active']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
