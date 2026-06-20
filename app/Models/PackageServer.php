<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageServer extends Model
{
    use BelongsToTenant;

    protected $guarded = ['id'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
