<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:34              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Console\Commands; use Illuminate\Console\Command; use App\Models\Tenant; use App\Models\User; use Illuminate\Support\Str; class MakeSysadmin extends Command { protected $signature = 'make:sysadmin'; protected $description = 'Create a default system admin user for tenant management'; public function handle() { goto vNTah; Nma0y: $this->line('Username: sysadmin'); goto C3ZCb; rxdbX: $this->line('---------------------------------'); goto Nma0y; FWWqr: $this->info('Creating Sysadmin User...'); goto HFlbz; vNTah: $this->info('Creating System Tenant...'); goto Ncg1z; hUzzG: $this->line('---------------------------------'); goto Sxmp4; Sxmp4: $this->line('Login directly to access the superadmin panel.'); goto yz2vW; qV6SZ: $this->info('Success! Sysadmin user is ready.'); goto rxdbX; Ncg1z: $VXSyW = Tenant::firstOrCreate(['slug' => 'system'], ['name' => 'System', 'uuid' => (string) Str::uuid(), 'is_active' => true]); goto FWWqr; C3ZCb: $this->line('Password: sysadmin'); goto hUzzG; HFlbz: $mNbko = User::withoutGlobalScopes()->updateOrCreate(['username' => 'sysadmin'], ['tenant_id' => $VXSyW->id, 'name' => 'System Administrator', 'email' => 'sysadmin@system.local', 'password' => 'sysadmin', 'is_system_admin' => true, 'is_active' => true]); goto qV6SZ; yz2vW: } }
