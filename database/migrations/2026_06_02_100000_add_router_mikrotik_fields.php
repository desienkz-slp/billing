<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->integer('ftp_port')->default(21)->after('port');
            $table->boolean('auto_backup')->default(false)->after('use_ssl');
            $table->boolean('use_radius')->default(false)->after('auto_backup');
            $table->text('radius_secret')->nullable()->after('use_radius');
        });
    }

    public function down(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropColumn(['ftp_port', 'auto_backup', 'use_radius', 'radius_secret']);
        });
    }
};
