<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;

/**
 * RadUserGroup — Mapping user ke group RADIUS.
 *
 * Contoh: username=john, groupname=paket-10m, priority=1
 * FreeRADIUS akan apply atribut dari radgroupreply berdasarkan group ini.
 */
class RadUserGroup extends Model
{
    protected $connection = 'radius';
    protected $table = 'radusergroup';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'groupname',
        'priority',
    ];
}
