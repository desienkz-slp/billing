<?php
namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IsolirJob extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['executed_at' => 'datetime']; public function customer(): BelongsTo { return $this->belongsTo(Customer::class); } }
