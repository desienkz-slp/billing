<?php
namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IsolirLog extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['success' => 'boolean']; public function customer(): BelongsTo { return $this->belongsTo(Customer::class); } public function executor(): BelongsTo { return $this->belongsTo(User::class, 'executed_by'); } }
