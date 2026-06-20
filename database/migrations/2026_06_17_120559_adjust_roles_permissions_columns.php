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
        Schema::table('roles', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'can_import_customer',
                'can_export_customer',
                'can_manage_routers',
                'can_access_mikrotik',
                'can_manage_config',
                'can_auto_isolir_config'
            ]);

            // Add new columns
            $table->boolean('can_view_dashboard_olt')->default(false)->after('can_view_dashboard_map');
            $table->boolean('can_import_export_customer')->default(false)->after('can_view_all_customers');
            $table->boolean('is_saldo_limited')->default(false)->after('fee_locked');
            $table->boolean('can_delete_customer_cuti')->default(false)->after('can_delete_customer');
            
            // Rename columns
            $table->renameColumn('can_view_map', 'can_config_map');
            $table->renameColumn('can_deduct_balance', 'can_manage_saldo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('can_config_map', 'can_view_map');
            $table->renameColumn('can_manage_saldo', 'can_deduct_balance');

            $table->dropColumn([
                'can_view_dashboard_olt',
                'can_import_export_customer',
                'is_saldo_limited',
                'can_delete_customer_cuti'
            ]);

            $table->boolean('can_import_customer')->default(false);
            $table->boolean('can_export_customer')->default(false);
            $table->boolean('can_manage_routers')->default(false);
            $table->boolean('can_access_mikrotik')->default(false);
            $table->boolean('can_manage_config')->default(false);
            $table->boolean('can_auto_isolir_config')->default(false);
        });
    }
};
