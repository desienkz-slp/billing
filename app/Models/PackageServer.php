<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Models; use App\Traits\BelongsToTenant; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; class PackageServer extends Model { use BelongsToTenant; protected $guarded = ['id']; public function package(): BelongsTo { return $this->belongsTo(Package::class); } public function server(): BelongsTo { return $this->belongsTo(Server::class); } }
