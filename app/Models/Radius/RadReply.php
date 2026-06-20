<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;

/**
 * RadReply — Atribut balasan ke NAS per user.
 *
 * Contoh: username=john, attribute=Framed-IP-Address, op=:=, value=10.0.0.100
 * Digunakan untuk assign IP, rate-limit per user, dll.
 */
class RadReply extends Model
{
    protected $connection = 'radius';
    protected $table = 'radreply';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'attribute',
        'op',
        'value',
    ];
}
