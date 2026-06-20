<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;

class MakeSysadmin extends Command
{
    protected $signature = 'make:sysadmin';
    protected $description = 'Create a default system admin user for tenant management';

    public function handle()
    {
        $this->info('Creating System Tenant...');
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'system'],
            [
                'name' => 'System',
                'uuid' => (string) Str::uuid(),
                'is_active' => true,
            ]
        );

        $this->info('Creating Sysadmin User...');
        $user = User::withoutGlobalScopes()->updateOrCreate(
            ['username' => 'sysadmin'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'System Administrator',
                'email' => 'sysadmin@system.local',
                'password' => 'sysadmin',
                'is_system_admin' => true,
                'is_active' => true,
            ]
        );

        $this->info('Success! Sysadmin user is ready.');
        $this->line('---------------------------------');
        $this->line('Username: sysadmin');
        $this->line('Password: sysadmin');
        $this->line('---------------------------------');
        $this->line('Login directly to access the superadmin panel.');
    }
}
