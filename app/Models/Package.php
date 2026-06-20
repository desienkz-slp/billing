<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use BelongsToTenant;

    protected $guarded = ['id'];
    protected $casts = ['is_active' => 'boolean'];

    public function customers(): HasMany { return $this->hasMany(Customer::class); }
    public function packageRouters(): HasMany { return $this->hasMany(PackageRouter::class); }
    public function packageServers(): HasMany { return $this->hasMany(PackageServer::class); }
    
    public function routers(): BelongsToMany
    {
        return $this->belongsToMany(Router::class, 'package_routers')
            ->withPivot('pppoe_profile', 'isolir_profile')
            ->withTimestamps();
    }

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'package_servers')
            ->withPivot('radius_group', 'radius_isolir_group')
            ->withTimestamps();
    }
}
