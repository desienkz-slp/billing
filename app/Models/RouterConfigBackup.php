<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouterConfigBackup extends Model
{
    protected $guarded = ['id'];

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }
}
