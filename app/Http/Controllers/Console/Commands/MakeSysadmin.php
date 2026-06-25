<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:50              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Console\Commands; use Illuminate\Console\Command; use App\Models\Tenant; use App\Models\User; use Illuminate\Support\Str; class MakeSysadmin extends Command { protected $signature = 'make:sysadmin'; protected $description = 'Create a default system admin user for tenant management'; public function handle() { goto kn8Wk; sPlF0: $this->line('Login directly to access the superadmin panel.'); goto VSXU_; iWD2e: $this->line('---------------------------------'); goto tO4M8; rHfSb: $this->info('Creating Sysadmin User...'); goto hlY0w; tO4M8: $this->line('Username: sysadmin'); goto x7JOv; VljUu: $this->info('Success! Sysadmin user is ready.'); goto iWD2e; kn8Wk: $this->info('Creating System Tenant...'); goto UIWlV; UIWlV: $sPiAm = Tenant::firstOrCreate(['slug' => 'system'], ['name' => 'System', 'uuid' => (string) Str::uuid(), 'is_active' => true]); goto rHfSb; x7JOv: $this->line('Password: sysadmin'); goto nYx5P; hlY0w: $Ie2cF = User::withoutGlobalScopes()->updateOrCreate(['username' => 'sysadmin'], ['tenant_id' => $sPiAm->id, 'name' => 'System Administrator', 'email' => 'sysadmin@system.local', 'password' => 'sysadmin', 'is_system_admin' => true, 'is_active' => true]); goto VljUu; nYx5P: $this->line('---------------------------------'); goto sPlF0; VSXU_: } }
