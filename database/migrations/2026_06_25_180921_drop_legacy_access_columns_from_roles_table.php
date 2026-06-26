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
            $table->dropColumn([
                'can_access_billing',
                'can_access_config',
                'can_access_db',
                'can_access_map'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('can_access_billing')->default(true);
            $table->boolean('can_access_config')->default(true);
            $table->boolean('can_access_db')->default(true);
            $table->boolean('can_access_map')->default(true);
        });
    }
};
