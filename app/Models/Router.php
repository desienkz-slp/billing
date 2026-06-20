<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Router extends Model
{
    use BelongsToTenant;

    protected $guarded = ['id'];
    protected $casts = [
        'is_active' => 'boolean',
        'use_ssl' => 'boolean',
        'auto_backup' => 'boolean',
        'use_radius' => 'boolean',
        'last_backup_at' => 'datetime',
    ];
    protected $hidden = ['password', 'radius_secret'];

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getDecryptedPassword(): string
    {
        return Crypt::decryptString($this->password);
    }

    public function setRadiusSecretAttribute($value): void
    {
        $this->attributes['radius_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getDecryptedRadiusSecret(): ?string
    {
        return $this->radius_secret ? Crypt::decryptString($this->radius_secret) : null;
    }

    public function customers(): HasMany { return $this->hasMany(Customer::class); }
    public function pppoeProfiles(): HasMany { return $this->hasMany(PppoeProfile::class); }
    public function pppoeSecrets(): HasMany { return $this->hasMany(PppoeSecret::class); }
    public function radiusServer(): BelongsTo { return $this->belongsTo(Server::class, 'radius_server_id'); }
}
