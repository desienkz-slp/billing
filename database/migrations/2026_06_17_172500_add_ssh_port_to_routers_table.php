<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            if (!Schema::hasColumn('routers', 'ssh_port')) {
                $table->integer('ssh_port')->default(22)->after('port');
            }
        });
    }

    public function down(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            if (Schema::hasColumn('routers', 'ssh_port')) {
                $table->dropColumn('ssh_port');
            }
        });
    }
};
