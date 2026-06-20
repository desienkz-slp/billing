<?php

namespace App\Services;

class PermissionService
{
    /**
     * Mapping permission lama (auth_permissions.php) → capability baru
     * Format: 'legacy_key' => 'dotted.capability.name'
     */
    public const CAPABILITIES = [
        // Dashboard
        'can_view_dashboard'     => 'billing.dashboard.view',

        // Customers
        'can_view_customers'     => 'billing.customers.view',
        'can_input_customer'     => 'billing.customers.create',
        'can_edit'               => 'billing.customers.edit',
        'can_delete'             => 'billing.customers.delete',
        'can_import_customer'    => 'billing.customers.import',
        'can_export_customer'    => 'billing.customers.export',

        // Payments
        'can_bayar'              => 'billing.payments.create',
        'can_cancel_bayar'       => 'billing.payments.cancel',
        'can_view_payments'      => 'billing.payments.view',

        // Invoices
        'can_view_invoices'      => 'billing.invoices.view',
        'can_print_nota'         => 'billing.invoices.print',

        // Isolir
        'can_isolir'             => 'billing.isolir.manage',
        'can_view_isolir'        => 'billing.isolir.view',

        // Cuti
        'can_manage_cuti'        => 'billing.cuti.manage',

        // Reports
        'can_view_laporan'       => 'billing.reports.view',
        'can_view_keuangan'      => 'billing.finance.view',
        'can_view_statistics'    => 'billing.statistics.view',

        // Finance (Sales)
        'can_input_pengeluaran'  => 'billing.expenses.create',
        'can_delete_pengeluaran' => 'billing.expenses.delete',
        'can_setoran'            => 'billing.deposits.create',
        'can_cancel_setoran'     => 'billing.deposits.cancel',
        'can_kurangi_saldo'      => 'billing.deposit.deduct',
        'can_view_saldo'         => 'billing.deposit.view',

        // Master Data
        'can_manage_paket'       => 'master.packages.manage',
        'can_manage_area'        => 'master.areas.manage',
        'can_manage_odp'         => 'master.odp.manage',
        'can_manage_router'      => 'master.routers.manage',
        'can_manage_server'      => 'master.servers.manage',

        // PPPoE & RADIUS
        'can_access_pppoe'       => 'monitor.pppoe.access',
        'can_access_radius'      => 'monitor.radius.access',
        'can_access_mikrotik'    => 'monitor.mikrotik.access',

        // Admin
        'can_manage_user'        => 'admin.users.manage',
        'can_manage_role'        => 'admin.roles.manage',
        'can_manage_settings'    => 'admin.settings.manage',
        'can_backup'             => 'admin.backup.manage',
        'can_view_audit_log'     => 'admin.audit.view',

        // WhatsApp
        'can_send_wa'            => 'billing.whatsapp.send',
        'can_manage_wa'          => 'billing.whatsapp.manage',

        // Map / GIS
        'can_view_map'           => 'monitor.map.view',
        'can_edit_coordinates'   => 'monitor.map.edit',
    ];

    /**
     * Default permissions per role
     */
    public const ROLE_DEFAULTS = [
        'Superadmin' => '*', // All permissions
        'Admin' => [
            'billing.dashboard.view',
            'billing.customers.*',
            'billing.payments.*',
            'billing.invoices.*',
            'billing.isolir.*',
            'billing.cuti.manage',
            'billing.reports.view',
            'billing.finance.view',
            'billing.statistics.view',
            'billing.expenses.*',
            'billing.deposits.*',
            'billing.deposit.*',
            'billing.whatsapp.*',
            'master.*',
            'monitor.*',
            'admin.users.manage',
            'admin.roles.manage',
            'admin.settings.manage',
            'admin.audit.view',
        ],
        'Sales' => [
            'billing.dashboard.view',
            'billing.customers.view',
            'billing.customers.create',
            'billing.customers.edit',
            'billing.payments.view',
            'billing.payments.create',
            'billing.invoices.view',
            'billing.invoices.print',
            'billing.isolir.view',
            'billing.expenses.create',
            'billing.deposits.create',
            'billing.deposit.view',
            'billing.whatsapp.send',
            'monitor.map.view',
        ],
        'Teknisi' => [
            'billing.dashboard.view',
            'billing.customers.view',
            'billing.isolir.view',
            'monitor.pppoe.access',
            'monitor.radius.access',
            'monitor.mikrotik.access',
            'monitor.map.view',
            'monitor.map.edit',
        ],
    ];

    /**
     * Check if a user has a specific capability
     */
    public function userCan(\App\Models\User $user, string $capability): bool
    {
        // Superadmin can do everything
        if ($user->isSuperAdmin()) {
            return true;
        }

        $role = $user->role;
        if (!$role) {
            return false;
        }

        // Hardcode check for permissions based on legacy boolean flags if available
        // For 'billing.payments.create', we check 'can_process_payment' or 'can_bayar'
        if ($capability === 'billing.payments.create') {
            return $role->hasPermission('can_process_payment') || $role->hasPermission('can_bayar');
        }

        // If a permissions array actually exists (e.g., from an accessor), we can check it
        $permissions = $role->permissions ?? [];
        if (empty($permissions)) {
            // Fallback to checking the Role_Defaults
            $permissions = self::getDefaultsForRole($role->name);
        }

        // Check wildcard
        if ($permissions === '*' || in_array('*', (array) $permissions)) {
            return true;
        }

        // Check exact match
        if (in_array($capability, (array) $permissions)) {
            return true;
        }

        // Check wildcard patterns (e.g., 'billing.customers.*')
        foreach ((array) $permissions as $perm) {
            if (str_ends_with($perm, '.*')) {
                $prefix = rtrim($perm, '.*');
                if (str_starts_with($capability, $prefix)) {
                    return true;
                }
            }
        }

        // Fallback mapping check
        $map = array_flip(self::CAPABILITIES);
        if (isset($map[$capability])) {
            $legacyKey = $map[$capability];
            return $role->hasPermission($legacyKey);
        }

        return false;
    }

    /**
     * Get all capabilities for a user
     */
    public function getUserCapabilities(\App\Models\User $user): array
    {
        if ($user->isSuperAdmin()) {
            return array_values(self::CAPABILITIES);
        }

        $role = $user->role;
        if (!$role || !$role->permissions) {
            return [];
        }

        $permissions = $role->permissions;

        if ($permissions === '*') {
            return array_values(self::CAPABILITIES);
        }

        $resolved = [];
        foreach ((array) $permissions as $perm) {
            if (str_ends_with($perm, '.*')) {
                $prefix = rtrim($perm, '.*');
                foreach (self::CAPABILITIES as $cap) {
                    if (str_starts_with($cap, $prefix)) {
                        $resolved[] = $cap;
                    }
                }
            } else {
                $resolved[] = $perm;
            }
        }

        return array_unique($resolved);
    }

    /**
     * Get default permissions for a role name
     */
    public static function getDefaultsForRole(string $roleName): array|string
    {
        return self::ROLE_DEFAULTS[$roleName] ?? [];
    }
}
