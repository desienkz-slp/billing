<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\HasMany; class Area extends Model { use BelongsToTenant; protected $fillable = ['tenant_id', 'name', 'description', 'is_active']; protected $casts = ['is_active' => 'boolean']; public function customers(): HasMany { return $this->hasMany(Customer::class); } public function odps(): HasMany { return $this->hasMany(Odp::class); } }
