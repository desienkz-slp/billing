<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Server extends Model
{
    use BelongsToTenant;

    protected $guarded = ['id'];
    protected $casts = ['is_active' => 'boolean'];
    protected $hidden = ['db_password', 'password', 'api_token'];

    // DB password (FreeRADIUS)
    public function setDbPasswordAttribute($v): void { $this->attributes['db_password'] = Crypt::encryptString($v); }
    public function getDecryptedDbPassword(): string { return Crypt::decryptString($this->db_password); }

    // Auth password (Geniacs)
    public function setPasswordAttribute($v): void { $this->attributes['password'] = $v ? Crypt::encryptString($v) : null; }
    public function getDecryptedPassword(): ?string { return $this->password ? Crypt::decryptString($this->password) : null; }

    // API Token (RADIUS APIs)
    public function setApiTokenAttribute($v): void { $this->attributes['api_token'] = $v ? Crypt::encryptString($v) : null; }
    public function getDecryptedApiToken(): ?string { return $this->api_token ? Crypt::decryptString($this->api_token) : null; }

    // Scopes
    public function scopeGeniacs(Builder $query): Builder { return $query->where('type', 'geniacs'); }
    public function scopeFreeradius(Builder $query): Builder { return $query->where('type', 'freeradius'); }
    public function scopeRadiusApi(Builder $query): Builder { return $query->whereIn('type', ['daloradius_api', 'radiusdesk_api']); }
}
