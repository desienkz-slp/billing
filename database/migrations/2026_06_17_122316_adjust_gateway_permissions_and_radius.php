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
            $table->renameColumn('can_access_radius', 'can_view_radius');
            $table->boolean('can_config_radius')->default(false)->after('can_manage_pppoe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('can_view_radius', 'can_access_radius');
            $table->dropColumn('can_config_radius');
        });
    }
};
