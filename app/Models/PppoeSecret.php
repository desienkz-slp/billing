<?php
namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PppoeSecret extends Model { use BelongsToTenant; protected $guarded = ['id']; protected $casts = ['disabled' => 'boolean', 'mikrotik_data' => 'array']; public function router(): BelongsTo { return $this->belongsTo(Router::class); } public function customer(): BelongsTo { return $this->belongsTo(Customer::class); } }
