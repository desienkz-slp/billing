<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodeDeleted extends Model
{
    use BelongsToTenant;

    protected $guarded = ['id'];

    protected $casts = [
        'count' => 'integer',
    ];

    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
