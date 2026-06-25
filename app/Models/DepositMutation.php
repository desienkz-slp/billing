<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use Illuminate\Database\Eloquent\Model; class DepositMutation extends Model { protected $guarded = ['id']; public function user() { return $this->belongsTo(User::class, 'user_id'); } public function creator() { return $this->belongsTo(User::class, 'created_by'); } }
