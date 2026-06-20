<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use BelongsToTenant;
    protected $guarded = ['id'];
    protected $casts = ['expense_date' => 'date'];
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
