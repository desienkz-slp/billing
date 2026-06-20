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
            $table->renameColumn('can_config_radius', 'can_manage_radius');
            $table->renameColumn('can_manage_pppoe', 'can_manage_router');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('can_manage_radius', 'can_config_radius');
            $table->renameColumn('can_manage_router', 'can_manage_pppoe');
        });
    }
};
