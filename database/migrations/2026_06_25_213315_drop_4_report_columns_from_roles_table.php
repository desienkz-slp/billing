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
                'can_view_menu_tax', 'can_view_menu_recap', 
                'can_view_menu_cashflow', 'can_view_menu_statistics'
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
