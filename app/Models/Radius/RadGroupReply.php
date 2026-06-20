<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;

/**
 * RadGroupReply — Atribut balasan per group.
 *
 * Contoh: groupname=paket-10m, attribute=Mikrotik-Rate-Limit, op=:=, value=10M/10M
 * Ini adalah cara FreeRADIUS mengatur bandwidth per paket.
 */
class RadGroupReply extends Model
{
    protected $connection = 'radius';
    protected $table = 'radgroupreply';
    public $timestamps = false;

    protected $fillable = [
        'groupname',
        'attribute',
        'op',
        'value',
    ];
}
