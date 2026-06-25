<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:29              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models\Radius; use Illuminate\Database\Eloquent\Model; class RadReply extends Model { protected $connection = 'radius'; protected $table = 'radreply'; public $timestamps = false; protected $fillable = ['username', 'attribute', 'op', 'value']; }