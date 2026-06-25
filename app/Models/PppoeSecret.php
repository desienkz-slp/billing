<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:28              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; class PppoeSecret extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['disabled' => 'boolean', 'mikrotik_data' => 'array']; public function router(): BelongsTo { return $this->belongsTo(Router::class); } public function customer(): BelongsTo { return $this->belongsTo(Customer::class); } }