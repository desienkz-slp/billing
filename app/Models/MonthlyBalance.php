<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyBalance extends Model
{
    use BelongsToTenant;
    protected $guarded = ['id'];
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
}
