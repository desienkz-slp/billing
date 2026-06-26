<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Set default permissions for existing roles.
 * Superadmin → all permissions true
 * Admin → most permissions true
 * Other roles → basic access only
 */
return new class extends Migration
{
    public function up(): void
    {
        // All boolean permission columns in the roles table
        $allPermissions = [
            'can_view_dashboard', 'can_input_customer', 'can_edit_customer',
            'can_delete_customer', 'can_import_customer', 'can_export_customer',
            'can_process_payment', 'can_cancel_payment', 'can_view_payment_history',
            'can_manage_isolir', 'can_auto_isolir_config',
            'can_cuti', 'can_manage_cuti',
            'can_view_reports', 'can_view_finance',
            'can_manage_expenses', 'can_manage_deposits', 'can_deduct_balance', 'can_delete_finance',
            'can_view_all_customers', 'can_use_deposit', 'can_edit_template',
            'can_manage_packages', 'can_manage_areas', 'can_manage_odp',
            'can_manage_routers', 'can_manage_servers',
            'can_manage_users', 'can_manage_roles',
            'can_view_audit_logs', 'can_manage_config', 'can_backup_restore',
            'can_access_mikrotik', 'can_access_radius', 'can_manage_pppoe',
            'can_send_wa_blast', 'can_view_map', 'can_view_monitor',
            'can_access_billing', 'can_access_config', 'can_access_db', 'can_access_map',
            'can_view_dashboard_config', 'can_view_dashboard_map',
        ];

        // ── Superadmin: ALL permissions true ──
        $superadminPerms = array_fill_keys($allPermissions, true);
        $superadminPerms['can_view_all_customers'] = true;
        $superadminPerms['view_by_area'] = false;
        $superadminPerms['view_by_sales'] = false;
        $superadminPerms['view_own_only'] = false;

        DB::table('roles')
            ->whereRaw("LOWER(name) = 'superadmin'")
            ->update($superadminPerms);

        // ── Admin: Most permissions true ──
        $adminPerms = array_fill_keys($allPermissions, true);
        $adminPerms['can_access_db'] = false;
        $adminPerms['can_view_all_customers'] = true;
        $adminPerms['view_by_area'] = false;
        $adminPerms['view_by_sales'] = false;
        $adminPerms['view_own_only'] = false;

        DB::table('roles')
            ->whereRaw("LOWER(name) = 'admin'")
            ->update($adminPerms);

        // ── Sales: Limited permissions ──
        $salesPerms = array_fill_keys($allPermissions, false);
        $salesPerms['can_view_dashboard'] = true;
        $salesPerms['can_input_customer'] = true;
        $salesPerms['can_edit_customer'] = true;
        $salesPerms['can_process_payment'] = true;
        $salesPerms['can_view_payment_history'] = true;
        $salesPerms['can_cuti'] = true;
        $salesPerms['can_view_reports'] = true;
        $salesPerms['can_manage_expenses'] = true;
        $salesPerms['can_manage_deposits'] = true;
        $salesPerms['can_use_deposit'] = true;
        $salesPerms['can_access_billing'] = true;
        $salesPerms['can_view_map'] = true;
        $salesPerms['view_by_sales'] = true; // Non-prefixed column
        $salesPerms['view_by_area'] = false;
        $salesPerms['view_own_only'] = false;
        $salesPerms['can_view_all_customers'] = false;

        DB::table('roles')
            ->whereRaw("LOWER(name) = 'sales'")
            ->update($salesPerms);

        // ── Teknisi: Very limited ──
        $teknisiPerms = array_fill_keys($allPermissions, false);
        $teknisiPerms['can_view_dashboard'] = true;
        $teknisiPerms['can_view_payment_history'] = true;
        $teknisiPerms['can_manage_isolir'] = true;
        $teknisiPerms['can_view_reports'] = true;
        $teknisiPerms['can_access_billing'] = true;
        $teknisiPerms['can_access_mikrotik'] = true;
        $teknisiPerms['can_access_radius'] = true;
        $teknisiPerms['can_manage_pppoe'] = true;
        $teknisiPerms['can_view_map'] = true;
        $teknisiPerms['can_view_monitor'] = true;
        $teknisiPerms['view_by_area'] = true; // Non-prefixed column
        $teknisiPerms['view_by_sales'] = false;
        $teknisiPerms['view_own_only'] = false;
        $teknisiPerms['can_view_all_customers'] = false;

        DB::table('roles')
            ->whereRaw("LOWER(name) = 'teknisi'")
            ->update($teknisiPerms);

        // ── Tim Tagih: Collection-focused ──
        $timTagihPerms = array_fill_keys($allPermissions, false);
        $timTagihPerms['can_view_dashboard'] = true;
        $timTagihPerms['can_process_payment'] = true;
        $timTagihPerms['can_view_payment_history'] = true;
        $timTagihPerms['can_manage_isolir'] = true;
        $timTagihPerms['can_cuti'] = true;
        $timTagihPerms['can_view_reports'] = true;
        $timTagihPerms['can_access_billing'] = true;
        $timTagihPerms['can_view_map'] = true;
        $timTagihPerms['view_by_sales'] = true; // Non-prefixed column
        $timTagihPerms['view_by_area'] = false;
        $timTagihPerms['view_own_only'] = false;
        $timTagihPerms['can_view_all_customers'] = false;

        DB::table('roles')
            ->whereRaw("LOWER(name) = 'tim_tagih'")
            ->update($timTagihPerms);
    }

    public function down(): void
    {
        // Reset all permission columns to false for all roles
        $allPermissions = [
            'can_view_dashboard', 'can_input_customer', 'can_edit_customer',
            'can_delete_customer', 'can_import_customer', 'can_export_customer',
            'can_process_payment', 'can_cancel_payment', 'can_view_payment_history',
            'can_manage_isolir', 'can_auto_isolir_config',
            'can_cuti', 'can_manage_cuti',
            'can_view_reports', 'can_view_finance',
            'can_manage_expenses', 'can_manage_deposits', 'can_deduct_balance', 'can_delete_finance',
            'can_view_all_customers', 'can_use_deposit', 'can_edit_template',
            'can_manage_packages', 'can_manage_areas', 'can_manage_odp',
            'can_manage_routers', 'can_manage_servers',
            'can_manage_users', 'can_manage_roles',
            'can_view_audit_logs', 'can_manage_config', 'can_backup_restore',
            'can_access_mikrotik', 'can_access_radius', 'can_manage_pppoe',
            'can_send_wa_blast', 'can_view_map', 'can_view_monitor',
            'can_access_billing', 'can_access_config', 'can_access_db', 'can_access_map',
            'can_view_dashboard_config', 'can_view_dashboard_map',
        ];

        DB::table('roles')->update(array_fill_keys($allPermissions, false));
    }
};
