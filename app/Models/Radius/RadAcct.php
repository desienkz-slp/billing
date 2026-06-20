<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;

/**
 * RadAcct — Accounting data FreeRADIUS (READ-ONLY).
 *
 * Mencatat session pelanggan: kapan connect, disconnect, berapa data yang dipakai.
 * Billing hanya membaca tabel ini untuk monitoring, tidak pernah menulis.
 */
class RadAcct extends Model
{
    protected $connection = 'radius';
    protected $table = 'radacct';
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'acctstarttime' => 'datetime',
        'acctstoptime' => 'datetime',
        'acctupdatetime' => 'datetime',
    ];
}
