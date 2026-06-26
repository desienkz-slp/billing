<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, BelongsToTenant;

    protected $fillable = [
        'uuid', 'tenant_id', 'role_id', 'username', 'name',
        'email', 'phone', 'password', 'is_active', 'is_system_admin',
        'is_default_sales', 'last_login_at', 'last_login_ip',
        'fee_persen', 'fee_fix', 'fcm_token',
        'perms_view_area_ids', 'perms_view_sales_ids'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system_admin' => 'boolean',
        'is_default_sales' => 'boolean',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'perms_view_area_ids' => 'array',
        'perms_view_sales_ids' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user is a Superadmin (tenant-level)
     */
    public function isSuperAdmin(): bool
    {
        return $this->role?->name === 'Superadmin';
    }

    /**
     * Check if user is a System Admin (cross-tenant owner)
     */
    public function isSystemAdmin(): bool
    {
        return (bool) $this->is_system_admin;
    }

    /**
     * Check permission using PermissionService
     */
    public function hasCapability(string $capability): bool
    {
        return app(\App\Services\PermissionService::class)->userCan($this, $capability);
    }

    /**
     * Legacy permission check (for backward compat)
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->role) return false;
        $col = str_starts_with($permission, 'can_') ? $permission : 'can_' . $permission;
        return (bool) ($this->role->$col ?? false);
    }

    public function collectedPayments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class, 'collected_by');
    }

    public function deposits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Deposit::class, 'sales_id');
    }

    public function customers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Customer::class, 'sales_id');
    }

    public function expenses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Expense::class, 'created_by');
    }
}
