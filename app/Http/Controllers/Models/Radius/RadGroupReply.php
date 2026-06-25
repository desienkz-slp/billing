<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models\Radius; use Illuminate\Database\Eloquent\Model; class RadGroupReply extends Model { protected $connection = 'radius'; protected $table = 'radgroupreply'; public $timestamps = false; protected $fillable = ['groupname', 'attribute', 'op', 'value']; }
