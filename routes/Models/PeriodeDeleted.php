<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; class PeriodeDeleted extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['count' => 'integer']; public function deletedByUser(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); } }
