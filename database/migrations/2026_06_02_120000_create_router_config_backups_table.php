<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('router_config_backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained()->onDelete('cascade');
            $table->string('config_name', 200);
            $table->text('config_text');
            $table->timestamps();

            $table->index(['router_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('router_config_backups');
    }
};
