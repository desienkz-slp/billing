<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use BelongsToTenant;

    protected $guarded = ['id'];

    protected $casts = [
        'allowed_area_ids' => 'array',
        'allowed_sales_ids' => 'array',
        'perms_menu_view' => 'array',
        // Booleans (auto-cast by Laravel for bool columns)
        'can_view_dashboard' => 'boolean',
        'can_input_customer' => 'boolean',
        'can_edit_customer' => 'boolean',
        'can_delete_customer' => 'boolean',
        'can_import_export_customer' => 'boolean',
        'can_process_payment' => 'boolean',
        'can_cancel_payment' => 'boolean',
        'can_view_payment_history' => 'boolean',
        'can_manage_isolir' => 'boolean',
        'can_cuti' => 'boolean',
        'can_manage_cuti' => 'boolean',
        'can_view_reports' => 'boolean',
        'can_view_finance' => 'boolean',
        'can_manage_expenses' => 'boolean',
        'can_manage_deposits' => 'boolean',
        'can_manage_saldo' => 'boolean',
        'can_delete_finance' => 'boolean',
        'can_view_all_customers' => 'boolean',
        'view_by_area' => 'boolean',
        'view_by_sales' => 'boolean',
        'view_own_only' => 'boolean',
        'can_use_deposit' => 'boolean',
        'can_edit_template' => 'boolean',
        'can_send_wa_invoice' => 'boolean',
        'can_manage_packages' => 'boolean',
        'can_manage_areas' => 'boolean',
        'can_manage_odp' => 'boolean',
        'can_manage_servers' => 'boolean',
        'can_manage_users' => 'boolean',
        'can_manage_roles' => 'boolean',
        'can_view_audit_logs' => 'boolean',
        'can_backup_restore' => 'boolean',
        'can_view_radius' => 'boolean',
        'can_view_acs' => 'boolean',
        'can_manage_radius' => 'boolean',
        'can_manage_router' => 'boolean',
        'can_manage_olt' => 'boolean',
        'can_manage_acs' => 'boolean',
        'can_send_wa_blast' => 'boolean',
        'can_config_map' => 'boolean',
        'can_view_monitor' => 'boolean',
        // Dashboard Modules (Legacy dropped)
        'can_view_dashboard_config' => 'boolean',
        'can_view_dashboard_map' => 'boolean',
        'can_view_dashboard_olt' => 'boolean',
        // Sidebar Billing Menus
        'can_view_menu_dashboard' => 'boolean',
        'can_view_menu_pelanggan' => 'boolean',
        'can_view_menu_pelanggan_cuti' => 'boolean',
        'can_view_menu_isolir' => 'boolean',
        'can_view_menu_income' => 'boolean',
        'can_view_menu_other_in' => 'boolean',
        'can_view_menu_expenses' => 'boolean',
        'can_view_menu_tax' => 'boolean',
        'can_view_menu_recap' => 'boolean',
        'can_view_menu_cashflow' => 'boolean',
        'can_view_menu_statistics' => 'boolean',
        'can_view_menu_fee' => 'boolean',
        'can_view_menu_setoran' => 'boolean',
        'can_view_menu_saldo' => 'boolean',
        'can_view_menu_mitra' => 'boolean',
        'can_view_menu_master_paket' => 'boolean',
        'can_view_menu_master_area' => 'boolean',
        // Fee
        'fee_persen' => 'decimal:2',
        'fee_fix' => 'decimal:2',
        'fee_locked' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Ambil semua permissions yang aktif (true).
     */
    public function getActivePermissions(): array
    {
        return collect($this->getAttributes())
            ->filter(fn($value, $key) => str_starts_with($key, 'can_') && $value)
            ->keys()
            ->toArray();
    }

    /**
     * Set semua permissions sekaligus.
     */
    public function setAllPermissions(bool $value): self
    {
        collect($this->getAttributes())
            ->keys()
            ->filter(fn($key) => str_starts_with($key, 'can_'))
            ->each(fn($key) => $this->$key = $value);
        return $this;
    }

    /**
     * Cek apakah role punya permission tertentu.
     */
    public function hasPermission(string $permission): bool
    {
        // Normalize: handle both 'can_edit_customer' and 'edit_customer'
        $key = str_starts_with($permission, 'can_') ? $permission : "can_{$permission}";

        if (array_key_exists($key, $this->getAttributes())) {
            return (bool) $this->getAttribute($key);
        }

        // Also check non-prefixed keys (view_by_area, view_own_only, etc.)
        if (array_key_exists($permission, $this->getAttributes())) {
            return (bool) $this->getAttribute($permission);
        }

        return false;
    }

    /**
     * Fee config untuk role ini.
     */
    public function getFeeConfig(): array
    {
        return [
            'fee_type' => $this->fee_type ?? 'none',
            'fee_persen' => (float) ($this->fee_persen ?? 0),
            'fee_fix' => (float) ($this->fee_fix ?? 0),
        ];
    }

    /**
     * Apakah role ini punya fee (sales/agent type).
     */
    public function hasFee(): bool
    {
        return in_array($this->fee_type ?? 'none', ['persen', 'fix'], true);
    }

    /**
     * Mode visibilitas data: all | area | sales | both | own
     */
    public function getVisibilityMode(): string
    {
        if ($this->can_view_all_customers) return 'all';
        if ($this->view_own_only) return 'own';

        $byArea = (bool) $this->view_by_area;
        $bySales = (bool) $this->view_by_sales;

        if ($byArea && $bySales) return 'both';
        if ($byArea) return 'area';
        if ($bySales) return 'sales';

        return 'all';
    }

    /**
     * Daftar modul yang bisa diakses role (App Gateway).
     */
    public function getAccessibleModules(): array
    {
        $modules = [];
        if ($this->can_view_dashboard) $modules[] = 'billing';
        if ($this->can_view_dashboard_config) $modules[] = 'config';
        if ($this->can_view_dashboard_map) $modules[] = 'map';
        if ($this->can_view_monitor) $modules[] = 'monitor';
        if ($this->can_view_radius) $modules[] = 'radius';
        if ($this->can_view_acs) $modules[] = 'acs';
        return $modules;
    }
}
