<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Tenant - Representasi ISP (penyewa) dalam sistem multi-tenant.
 * 
 * Setiap ISP memiliki tenant_id unik yang menjadi kunci isolasi data.
 * Tenant TIDAK menggunakan BelongsToTenant trait karena tabel ini adalah root.
 */
class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'domain',
        'company_profile',
        'billing_config',
        'timezone',
        'is_active',
    ];

    protected $casts = [
        'company_profile' => 'array',
        'billing_config' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant) {
            if (empty($tenant->uuid)) {
                $tenant->uuid = (string) Str::uuid();
            }
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    // ==========================================
    // Relationships
    // ==========================================

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    public function routers(): HasMany
    {
        return $this->hasMany(Router::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class);
    }

    // ==========================================
    // Accessors & Helpers
    // ==========================================

    /**
     * Cek apakah tenant memiliki langganan aktif.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->whereIn('status', ['active', 'grace'])
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Ambil config billing dengan default values.
     */
    public function getBillingConfigValue(string $key, $default = null)
    {
        return data_get($this->billing_config, $key, $default);
    }

    /**
     * Ambil data company profile.
     */
    public function getCompanyName(): string
    {
        return data_get($this->company_profile, 'name', $this->name);
    }

    public function getCompanyAddress(): ?string
    {
        return data_get($this->company_profile, 'address');
    }

    public function getCompanyPhone(): ?string
    {
        return data_get($this->company_profile, 'phone');
    }

    public function getPpnRate(): float
    {
        return (float) $this->getBillingConfigValue('ppn_rate', 11);
    }
}
