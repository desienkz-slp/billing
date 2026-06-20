<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use BelongsToTenant;
    protected $guarded = ['id'];
    protected $casts = ['deposit_date' => 'date', 'cancelled_at' => 'datetime'];
    public function sales(): BelongsTo { return $this->belongsTo(User::class, 'sales_id'); }
    public function receiver(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
}
