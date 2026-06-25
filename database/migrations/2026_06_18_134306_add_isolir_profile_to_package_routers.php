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
        if (!Schema::hasColumn('package_routers', 'isolir_profile')) {
            Schema::table('package_routers', function (Blueprint $table) {
                $table->string('isolir_profile', 100)->nullable()->after('pppoe_profile');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_routers', function (Blueprint $table) {
            $table->dropColumn('isolir_profile');
        });
    }
};
