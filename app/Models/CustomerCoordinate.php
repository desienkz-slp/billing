<?php
namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CustomerCoordinate extends Model
{
    use BelongsToTenant;
    protected $guarded = ['id'];
    protected $casts = ['latitude' => 'decimal:7', 'longitude' => 'decimal:7'];
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
}
