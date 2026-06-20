<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ODC — Optical Distribution Cabinet.
 * Hierarki FTTH: OLT → ODC → ODP → Customer
 */
class Odc extends Model
{
    use BelongsToTenant;

    protected $guarded = ['id'];
    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function area(): BelongsTo { return $this->belongsTo(Area::class); }
    public function odps(): HasMany { return $this->hasMany(Odp::class); }

    public function getAvailablePorts(): int
    {
        return max(0, $this->capacity - $this->used);
    }
}
