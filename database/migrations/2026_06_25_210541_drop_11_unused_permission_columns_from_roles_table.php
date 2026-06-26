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
            $columnsToDrop = [
                'can_manage_olt', 'can_manage_acs', 'can_use_deposit',
                'can_view_payment_history', 'can_import_export_customer',
                'can_delete_customer_cuti', 'can_cancel_payment',
                'can_manage_areas', 'can_manage_odp', 'can_manage_servers',
                'can_edit_template'
            ];
            
            // Only drop columns that actually exist to prevent errors
            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('roles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Irreversible cleanly without knowing defaults
        });
    }
};
