<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function customers(): HasMany { return $this->hasMany(Customer::class); }
    public function odps(): HasMany { return $this->hasMany(Odp::class); }
}
