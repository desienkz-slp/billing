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
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('can_view_menu_dashboard')->default(false);
            $table->boolean('can_view_menu_pelanggan')->default(false);
            $table->boolean('can_view_menu_pelanggan_cuti')->default(false);
            $table->boolean('can_view_menu_isolir')->default(false);
            
            $table->boolean('can_view_menu_income')->default(false);
            $table->boolean('can_view_menu_other_in')->default(false);
            $table->boolean('can_view_menu_expenses')->default(false);
            $table->boolean('can_view_menu_tax')->default(false);
            $table->boolean('can_view_menu_recap')->default(false);
            $table->boolean('can_view_menu_cashflow')->default(false);
            $table->boolean('can_view_menu_statistics')->default(false);
            
            $table->boolean('can_view_menu_fee')->default(false);
            $table->boolean('can_view_menu_setoran')->default(false);
            $table->boolean('can_view_menu_saldo')->default(false);
            $table->boolean('can_view_menu_mitra')->default(false);
            
            $table->boolean('can_view_menu_master_paket')->default(false);
            $table->boolean('can_view_menu_master_area')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn([
                'can_view_menu_dashboard',
                'can_view_menu_pelanggan',
                'can_view_menu_pelanggan_cuti',
                'can_view_menu_isolir',
                'can_view_menu_income',
                'can_view_menu_other_in',
                'can_view_menu_expenses',
                'can_view_menu_tax',
                'can_view_menu_recap',
                'can_view_menu_cashflow',
                'can_view_menu_statistics',
                'can_view_menu_fee',
                'can_view_menu_setoran',
                'can_view_menu_saldo',
                'can_view_menu_mitra',
                'can_view_menu_master_paket',
                'can_view_menu_master_area',
            ]);
        });
    }
};
