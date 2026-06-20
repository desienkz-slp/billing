<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ODC - Optical Distribution Cabinet.
 * Level di atas ODP dalam hierarki jaringan FTTH:
 * OLT → ODC → ODP → Customer
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->string('location')->nullable();
            $table->integer('capacity')->default(96);       // jumlah port splitter
            $table->integer('used')->default(0);             // port terpakai
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'area_id']);
        });

        // Add odc_id to odps table (ODP belongs to ODC)
        Schema::table('odps', function (Blueprint $table) {
            $table->foreignId('odc_id')->nullable()->after('area_id')->constrained('odcs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('odps', function (Blueprint $table) {
            $table->dropConstrainedForeignId('odc_id');
        });
        Schema::dropIfExists('odcs');
    }
};
