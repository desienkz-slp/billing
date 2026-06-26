<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:32              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; class EmployeeLocation extends Model { use HasFactory, BelongsToTenant; protected $fillable = ['tenant_id', 'user_id', 'latitude', 'longitude', 'battery_level', 'recorded_at']; protected $casts = ['latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'battery_level' => 'integer', 'recorded_at' => 'datetime']; public function user() { return $this->belongsTo(User::class); } }
