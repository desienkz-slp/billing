<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Package;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 0. Create System tenant (for sysadmin)
        // ==========================================
        $systemTenant = Tenant::firstOrCreate(
            ['slug' => 'system'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'System',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'sysadmin'],
            [
                'tenant_id' => $systemTenant->id,
                'role_id' => null,
                'name' => 'System Administrator',
                'email' => 'sysadmin@system.local',
                'password' => 'sysadmin',
                'is_system_admin' => true,
                'is_active' => true,
            ]
        );

        // ==========================================
        // 1. Create default tenant
        // ==========================================
        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'ISP Demo',
            'slug' => 'demo',
            'domain' => null,
            'company_profile' => [
                'name' => 'ISP Demo',
                'address' => 'Jl. Contoh No. 1, Blitar',
                'phone' => '08123456789',
                'email' => 'admin@ispdemo.com',
                'logo' => null,
            ],
            'billing_config' => [
                'ppn_rate' => 11,
                'auto_isolir_days' => 20,
                'billing_cycle_day' => 1,
                'invoice_prefix' => 'INV',
                'receipt_prefix' => 'RCP',
                'currency' => 'IDR',
            ],
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ]);

        // ==========================================
        // 2. Create tenant subscription
        // ==========================================
        TenantSubscription::create([
            'tenant_id' => $tenant->id,
            'plan' => 'unlimited',
            'license_key' => 'DEMO-' . strtoupper(Str::random(16)),
            'max_customers' => 9999,
            'features' => ['all'],
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
            'status' => 'active',
        ]);

        // Bind tenant untuk auto-set tenant_id
        app()->instance('current_tenant', $tenant);

        // ==========================================
        // 3. Create roles
        // ==========================================
        $superadmin = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Superadmin',
            'description' => 'Akses penuh ke semua fitur',
            // All permissions = true
            'can_view_dashboard' => true, 'can_input_customer' => true, 'can_edit_customer' => true,
            'can_delete_customer' => true, 'can_import_customer' => true, 'can_export_customer' => true,
            'can_process_payment' => true, 'can_cancel_payment' => true, 'can_view_payment_history' => true,
            'can_manage_isolir' => true, 'can_auto_isolir_config' => true,
            'can_view_reports' => true, 'can_view_finance' => true, 'can_manage_expenses' => true,
            'can_manage_deposits' => true, 'can_deduct_balance' => true,
            'can_manage_packages' => true, 'can_manage_areas' => true, 'can_manage_odp' => true,
            'can_manage_routers' => true, 'can_manage_servers' => true,
            'can_manage_users' => true, 'can_manage_roles' => true, 'can_view_audit_logs' => true,
            'can_manage_config' => true, 'can_backup_restore' => true,
            'can_access_mikrotik' => true, 'can_access_radius' => true, 'can_manage_pppoe' => true,
            'can_send_wa_blast' => true, 'can_view_map' => true, 'can_view_monitor' => true,
        ]);

        $admin = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'description' => 'Administrasi umum tanpa akses teknikal & konfigurasi',
            'can_view_dashboard' => true, 'can_input_customer' => true, 'can_edit_customer' => true,
            'can_export_customer' => true,
            'can_process_payment' => true, 'can_view_payment_history' => true,
            'can_manage_isolir' => true,
            'can_view_reports' => true, 'can_view_finance' => true, 'can_manage_expenses' => true,
            'can_manage_deposits' => true,
            'can_manage_packages' => true, 'can_manage_areas' => true, 'can_manage_odp' => true,
            'can_send_wa_blast' => true, 'can_view_map' => true, 'can_view_monitor' => true,
        ]);

        Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Sales',
            'description' => 'Penagihan dan input pembayaran',
            'can_view_dashboard' => true, 'can_input_customer' => true,
            'can_process_payment' => true, 'can_view_payment_history' => true,
            'can_view_map' => true,
        ]);

        Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Teknisi',
            'description' => 'Akses teknikal — router, PPPoE, RADIUS',
            'can_view_dashboard' => true,
            'can_access_mikrotik' => true, 'can_access_radius' => true, 'can_manage_pppoe' => true,
            'can_manage_routers' => true, 'can_manage_odp' => true,
            'can_view_map' => true, 'can_view_monitor' => true,
        ]);

        Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Agent',
            'description' => 'Mitra/Agen penjualan di lapangan',
            'can_view_dashboard' => true, 'can_input_customer' => true,
            'can_view_map' => true,
        ]);

        Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'CS',
            'description' => 'Customer Service untuk menangani komplain dan bantuan',
            'can_view_dashboard' => true, 'can_view_map' => true,
            'can_send_wa_blast' => true, 'can_manage_isolir' => true,
        ]);

        Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Finance',
            'description' => 'Manajemen keuangan, kasir, dan laporan laba-rugi',
            'can_view_dashboard' => true, 'can_view_reports' => true, 'can_view_finance' => true,
            'can_manage_expenses' => true, 'can_manage_deposits' => true, 'can_deduct_balance' => true,
            'can_process_payment' => true, 'can_view_payment_history' => true,
        ]);

        // ==========================================
        // 4. Create default superadmin user
        // ==========================================
        User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $superadmin->id,
            'username' => 'superadmin',
            'name' => 'Super Administrator',
            'email' => 'admin@ispdemo.com',
            'password' => 'admin123',
            'is_active' => true,
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $admin->id,
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin2@ispdemo.com',
            'password' => 'admin123',
            'is_active' => true,
        ]);

        // ==========================================
        // 5. Sample areas
        // ==========================================
        Area::create(['tenant_id' => $tenant->id, 'name' => 'Kota Blitar', 'is_active' => true]);
        Area::create(['tenant_id' => $tenant->id, 'name' => 'Kab. Blitar', 'is_active' => true]);
        Area::create(['tenant_id' => $tenant->id, 'name' => 'Kota Kediri', 'is_active' => true]);

        // ==========================================
        // 6. Sample packages
        // ==========================================
        $packages = [
            ['name' => 'Paket 10M', 'speed' => '10M/10M', 'price' => 100000, 'pppoe_profile' => '10M'],
            ['name' => 'Paket 20M', 'speed' => '20M/20M', 'price' => 150000, 'pppoe_profile' => '20M'],
            ['name' => 'Paket 30M', 'speed' => '30M/30M', 'price' => 200000, 'pppoe_profile' => '30M'],
            ['name' => 'Paket 50M', 'speed' => '50M/50M', 'price' => 300000, 'pppoe_profile' => '50M'],
            ['name' => 'Paket 100M', 'speed' => '100M/100M', 'price' => 500000, 'pppoe_profile' => '100M'],
        ];

        foreach ($packages as $i => $pkg) {
            Package::create(array_merge($pkg, [
                'tenant_id' => $tenant->id,
                'price_ppn' => (int) round($pkg['price'] * 1.11),
                'is_active' => true,
                'sort_order' => $i,
            ]));
        }

        $this->command->info('✅ Seeder completed: 1 tenant, 4 roles, 2 users, 3 areas, 5 packages');
    }
}
