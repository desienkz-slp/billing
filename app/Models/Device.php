<?php
namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['is_active' => 'boolean']; public function user(): BelongsTo { return $this->belongsTo(User::class); } }
