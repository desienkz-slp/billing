<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; class CustomerCoordinate extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['latitude' => 'decimal:7', 'longitude' => 'decimal:7']; public function customer(): BelongsTo { return $this->belongsTo(Customer::class); } }
