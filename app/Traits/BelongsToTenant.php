<?php

namespace App\Traits;

use App\Models\Tenant;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BelongsToTenant - Trait untuk semua model yang tenant-aware.
 * 
 * Menambahkan:
 * - Global scope auto-filter tenant_id
 * - Auto-set tenant_id saat creating
 * - Relasi ke Tenant model
 * 
 * Usage: class Customer extends Model { use BelongsToTenant; }
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Auto-filter semua query berdasarkan tenant aktif
        static::addGlobalScope(new TenantScope);

        // Auto-set tenant_id saat membuat record baru
        static::creating(function ($model) {
            if (!$model->tenant_id && app()->has('current_tenant')) {
                $tenant = app('current_tenant');
                if ($tenant) {
                    $model->tenant_id = $tenant->id;
                }
            }
        });
    }

    /**
     * Relasi ke Tenant model.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Query tanpa tenant filter (untuk admin/superadmin global).
     */
    public function scopeWithoutTenant($query)
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }
}
