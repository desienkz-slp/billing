<?php
namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepositLedgerEntry extends Model { use BelongsToTenant; protected $guarded = ['id']; public function sales(): BelongsTo { return $this->belongsTo(User::class, 'sales_id'); } }
