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
            $table->integer('tgl_isolir')->nullable()->after('billing_date');
            $table->decimal('diskon', 15, 2)->nullable()->after('custom_price');
            $table->string('tambahan_layanan')->nullable()->after('notes');
            $table->text('deskripsi_layanan')->nullable()->after('tambahan_layanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'tgl_isolir',
                'diskon',
                'tambahan_layanan',
                'deskripsi_layanan'
            ]);
        });
    }
};
