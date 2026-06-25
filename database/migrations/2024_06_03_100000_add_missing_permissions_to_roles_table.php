<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // ── Cuti & Pengelolaan Cuti ─────────────────
            $table->boolean('can_cuti')->default(false)->after('can_manage_isolir');
            $table->boolean('can_manage_cuti')->default(false)->after('can_cuti');

            // ── Finance granular ─────────────────────────
            $table->boolean('can_delete_finance')->default(false)->after('can_deduct_balance');

            // ── Visibilitas data pelanggan ────────────────
            $table->boolean('can_view_all_customers')->default(false)->after('can_delete_finance');
            $table->boolean('view_by_area')->default(false)->after('can_view_all_customers');
            $table->boolean('view_by_sales')->default(true)->after('view_by_area');
            $table->boolean('view_own_only')->default(false)->after('view_by_sales');

            // ── Deposit system ───────────────────────────
            $table->boolean('can_use_deposit')->default(false)->after('view_own_only');

            // ── Template editing ─────────────────────────
            $table->boolean('can_edit_template')->default(false)->after('can_use_deposit');

            // ── Module-level access toggles ──────────────
            $table->boolean('can_access_billing')->default(true)->after('can_edit_template');
            $table->boolean('can_access_config')->default(false)->after('can_access_billing');
            $table->boolean('can_access_db')->default(false)->after('can_access_config');
            $table->boolean('can_access_map')->default(false)->after('can_access_db');

            // ── Dashboard toggles ────────────────────────
            $table->boolean('can_view_dashboard_config')->default(false)->after('can_access_map');
            $table->boolean('can_view_dashboard_map')->default(false)->after('can_view_dashboard_config');

            // ── Fee configuration ────────────────────────
            $table->string('fee_type', 10)->default('none')->after('can_view_dashboard_map');
            $table->decimal('fee_persen', 5, 2)->default(0)->after('fee_type');
            $table->decimal('fee_fix', 12, 2)->default(0)->after('fee_persen');

            // ── Menu view (granular per-page access) ─────
            $table->jsonb('perms_menu_view')->nullable()->after('fee_fix');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn([
                'can_cuti',
                'can_manage_cuti',
                'can_delete_finance',
                'can_view_all_customers',
                'view_by_area',
                'view_by_sales',
                'view_own_only',
                'can_use_deposit',
                'can_edit_template',
                'can_access_billing',
                'can_access_config',
                'can_access_db',
                'can_access_map',
                'can_view_dashboard_config',
                'can_view_dashboard_map',
                'fee_type',
                'fee_persen',
                'fee_fix',
                'perms_menu_view',
            ]);
        });
    }
};
