<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models\Radius; use Illuminate\Database\Eloquent\Model; class RadCheck extends Model { protected $connection = 'radius'; protected $table = 'radcheck'; public $timestamps = false; protected $fillable = ['username', 'attribute', 'op', 'value']; }
