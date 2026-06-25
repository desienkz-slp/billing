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
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('auto_isolir')->default(true)->after('status');
            $table->integer('max_tunggakan')->default(1)->after('auto_isolir');
            $table->boolean('pakai_ppn')->default(false)->after('max_tunggakan');
            $table->boolean('auto_wa_tagihan')->default(true)->after('pakai_ppn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'auto_isolir',
                'max_tunggakan',
                'pakai_ppn',
                'auto_wa_tagihan'
            ]);
        });
    }
};
