<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Setoran (deposit/setor dari sales ke perusahaan)
        if (Schema::hasTable('setorans')) { Schema::drop('setorans'); }
        Schema::create('setorans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('user_id');
            $table->integer('amount');
            $table->string('period', 7);
            $table->string('metode', 30)->default('cash');
            $table->text('keterangan')->nullable();
            $table->string('status', 20)->default('pending'); // pending, confirmed, cancelled
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'user_id']);
            $table->index(['tenant_id', 'period']);
            $table->index(['tenant_id', 'status']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('confirmed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // Package History (riwayat perubahan paket pelanggan)
        if (Schema::hasTable('package_histories')) { Schema::drop('package_histories'); }
        Schema::create('package_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('old_package_id')->nullable();
            $table->unsignedBigInteger('new_package_id')->nullable();
            $table->integer('old_price')->nullable();
            $table->integer('new_price')->nullable();
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'customer_id']);
            $table->foreign('old_package_id')->references('id')->on('packages')->nullOnDelete();
            $table->foreign('new_package_id')->references('id')->on('packages')->nullOnDelete();
            $table->foreign('changed_by')->references('id')->on('users')->nullOnDelete();
        });

        // Package-Router mapping (paket terkait router mana + profil PPPoE)
        if (Schema::hasTable('package_routers')) { Schema::drop('package_routers'); }
        Schema::create('package_routers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('router_id')->constrained()->cascadeOnDelete();
            $table->string('pppoe_profile', 100)->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'package_id', 'router_id']);
        });

        // App Notifications (in-app notifications — renamed to avoid Laravel built-in conflict)
        if (Schema::hasTable('app_notifications')) { Schema::drop('app_notifications'); }
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('message');
            $table->string('type', 50)->nullable();
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['tenant_id', 'user_id', 'is_read']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // Periode Deleted (tracking hapus data periode keuangan)
        if (Schema::hasTable('periode_deleteds')) { Schema::drop('periode_deleteds'); }
        Schema::create('periode_deleteds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('period', 7);
            $table->string('type', 30); // payment, expense, etc
            $table->integer('count')->default(0);
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'period']);
            $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_deleteds');
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('package_routers');
        Schema::dropIfExists('package_histories');
        Schema::dropIfExists('setorans');
    }
};
