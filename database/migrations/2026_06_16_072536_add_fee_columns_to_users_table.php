<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'fee_persen')) {
                $table->decimal('fee_persen', 5, 2)->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('users', 'fee_fix')) {
                $table->integer('fee_fix')->default(0)->after('fee_persen');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fee_persen', 'fee_fix']);
        });
    }
};
