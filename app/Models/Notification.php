<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; class Notification extends Model { use BelongsToTenant; protected $table = 'app_notifications'; protected $guarded = ['id']; protected $casts = ['is_read' => 'boolean']; public function user(): BelongsTo { return $this->belongsTo(User::class); } public function scopeUnread($Oa5s4) { return $Oa5s4->where('is_read', false); } public function markAsRead(): void { $this->update(['is_read' => true]); } }
