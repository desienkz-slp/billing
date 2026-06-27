<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models\Radius; use Illuminate\Database\Eloquent\Model; class RadAcct extends Model { protected $connection = 'radius'; protected $table = 'radacct'; public $timestamps = false; protected $guarded = []; protected $casts = ['acctstarttime' => 'datetime', 'acctstoptime' => 'datetime', 'acctupdatetime' => 'datetime']; }
