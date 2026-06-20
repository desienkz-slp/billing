<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;

/**
 * RadCheck — Tabel kredensial user FreeRADIUS.
 *
 * Setiap row = satu atribut auth untuk satu username.
 * Contoh: username=john, attribute=Cleartext-Password, op=:=, value=secret123
 *
 * Tabel ini ada di database RADIUS (MySQL remote), bukan di billing DB.
 */
class RadCheck extends Model
{
    protected $connection = 'radius';
    protected $table = 'radcheck';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'attribute',
        'op',
        'value',
    ];
}
