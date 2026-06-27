<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; class Deposit extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['deposit_date' => 'date', 'cancelled_at' => 'datetime']; public function sales(): BelongsTo { return $this->belongsTo(User::class, 'sales_id'); } public function receiver(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); } }
