<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; class Odp extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['is_active' => 'boolean', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7']; public function area(): BelongsTo { return $this->belongsTo(Area::class); } public function odc(): BelongsTo { return $this->belongsTo(Odc::class); } public function customers(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(Customer::class, 'odp_id'); } public function getAvailablePorts(): int { return max(0, $this->capacity - $this->used); } }
