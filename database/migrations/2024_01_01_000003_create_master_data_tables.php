<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Master data tables: areas, packages, routers, servers, ODPs.
 * Semua tabel memiliki tenant_id untuk multi-tenant isolation.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Areas / Wilayah layanan
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'name']);
        });

        // Packages / Paket internet
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('speed', 50)->nullable();       // e.g. "10M/10M"
            $table->integer('price');                       // dalam Rupiah (bukan cents)
            $table->integer('price_ppn')->default(0);       // harga + PPN
            $table->string('pppoe_profile', 100)->nullable(); // nama profile di MikroTik
            $table->string('radius_group', 100)->nullable();  // group di RADIUS
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
        });

        // Routers / MikroTik devices (Server 2)
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('host', 255);                    // IP/hostname
            $table->integer('port')->default(8728);
            $table->string('username', 100);
            $table->text('password');                        // encrypted via Laravel Crypt
            $table->boolean('use_ssl')->default(false);
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_backup_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
        });

        // Servers / RADIUS servers (Server 3)
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('host', 255);
            $table->integer('port')->default(3306);
            $table->string('db_name', 100)->default('radius');
            $table->string('db_username', 100);
            $table->text('db_password');                     // encrypted
            $table->string('type', 50)->default('freeradius'); // freeradius, mikrotik
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'type']);
        });

        // ODP - Optical Distribution Points
        Schema::create('odps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->string('location')->nullable();
            $table->integer('capacity')->default(8);        // jumlah port
            $table->integer('used')->default(0);            // port terpakai
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odps');
        Schema::dropIfExists('servers');
        Schema::dropIfExists('routers');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('areas');
    }
};
