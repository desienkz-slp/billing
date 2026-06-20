<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'pakai_bhp')) {
                $table->boolean('pakai_bhp')->default(false)->after('pakai_ppn');
            }
            if (!Schema::hasColumn('customers', 'pakai_admin')) {
                $table->boolean('pakai_admin')->default(false)->after('pakai_bhp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['pakai_bhp', 'pakai_admin']);
        });
    }
};
