<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; class PackageHistory extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['old_price' => 'integer', 'new_price' => 'integer']; public function customer(): BelongsTo { return $this->belongsTo(Customer::class); } public function oldPackage(): BelongsTo { return $this->belongsTo(Package::class, 'old_package_id'); } public function newPackage(): BelongsTo { return $this->belongsTo(Package::class, 'new_package_id'); } public function changedByUser(): BelongsTo { return $this->belongsTo(User::class, 'changed_by'); } }
