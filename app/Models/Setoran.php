<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:32              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; class Setoran extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['amount' => 'integer', 'confirmed_at' => 'datetime']; public function user(): BelongsTo { return $this->belongsTo(User::class); } public function customer(): BelongsTo { return $this->belongsTo(Customer::class); } public function payment(): BelongsTo { return $this->belongsTo(Payment::class); } public function confirmedBy(): BelongsTo { return $this->belongsTo(User::class, 'confirmed_by'); } public function createdByUser(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); } }
