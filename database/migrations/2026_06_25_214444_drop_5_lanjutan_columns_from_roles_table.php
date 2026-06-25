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
                'can_manage_users', 'can_manage_roles', 
                'can_view_audit_logs', 'can_backup_restore', 'can_send_wa_blast'
            ];
            
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
            //
        });
    }
};
