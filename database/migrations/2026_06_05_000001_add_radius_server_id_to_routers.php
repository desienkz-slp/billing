<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->foreignId('radius_server_id')
                ->nullable()
                ->after('use_radius')
                ->constrained('servers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('radius_server_id');
        });
    }
};
