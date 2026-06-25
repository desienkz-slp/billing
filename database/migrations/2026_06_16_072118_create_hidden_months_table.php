<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hidden_months', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('periode', 7); // Format: YYYY-MM
            $table->timestamps();

            $table->unique(['tenant_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hidden_months');
    }
};
