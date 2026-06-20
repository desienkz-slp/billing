<?php

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class SyncCustomerToDbPusat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $customer;
    public $action; // 'create', 'update', 'delete'

    public function __construct(Customer $customer, string $action)
    {
        $this->customer = $customer;
        $this->action = $action;
    }

    public function handle()
    {
        // Don't sync if customer disabled central sync (if that flag exists)
        if (isset($this->customer->sync_db_pusat) && !$this->customer->sync_db_pusat) {
            return;
        }

        // Retrieve configs
        $configs = DB::table('billing_configs')
            ->where('tenant_id', $this->customer->tenant_id)
            ->whereIn('key', [
                'db_pusat_sync_method', 'db_pusat_type', 'db_pusat_url', 
                'db_pusat_token', 'db_pusat_username', 'db_pusat_password'
            ])
            ->pluck('value', 'key')->toArray();

        if (empty($configs)) return;

        // Ensure push is enabled (or sync method doesn't exist which defaults to push/pull both)
        $syncMethod = $configs['db_pusat_sync_method'] ?? 'push';
        if ($syncMethod === 'pull') return; // Only pull, do not push

        $type = $configs['db_pusat_type'] ?? 'api';
        $package = $this->customer->package;
        
        $profile = $package->radius_group ?? 'default';

        // Isolir Logic
        if ($this->customer->status === 'inactive' && !empty($package->radius_group_isolir)) {
            $profile = $package->radius_group_isolir;
            // If action was delete due to isolir, we might actually want to update it to isolir profile
            if ($this->action === 'delete') {
                $this->action = 'update';
            }
        }

        try {
            if ($type === 'api') {
                $this->pushToApi($configs, $profile);
            } else {
                $this->pushToDatabase($configs, $type, $profile);
            }
        } catch (\Exception $e) {
            Log::error("SyncCustomerToDbPusat Error: " . $e->getMessage());
            // Retry logic could be added here
        }
    }

    private function pushToApi($configs, $profile)
    {
        $url = $configs['db_pusat_url'] ?? '';
        if (!$url) return;

        $authType = $configs['db_pusat_auth_type'] ?? 'token';
        
        $request = Http::timeout(10);
        if ($authType === 'token') {
            $request->withToken($configs['db_pusat_token'] ?? '');
        } else {
            $request->withBasicAuth($configs['db_pusat_username'] ?? '', $configs['db_pusat_password'] ?? '');
        }

        $payload = [
            'username' => $this->customer->username,
            'password' => $this->customer->getDecryptedPassword(),
            'profile' => $profile,
            'action' => $this->action,
            'status' => $this->customer->status
        ];

        $request->post(rtrim($url, '/') . '/sync-user', $payload);
    }

    private function pushToDatabase($configs, $driver, $profile)
    {
        $host = str_replace(['http://', 'https://'], '', $configs['db_pusat_url'] ?? '127.0.0.1');
        $host = explode('/', $host)[0];
        $host = explode(':', $host)[0];

        Config::set('database.connections.db_pusat_push', [
            'driver' => $driver,
            'host' => $host,
            'port' => $driver === 'pgsql' ? '5432' : '3306',
            'database' => 'radius',
            'username' => $configs['db_pusat_username'] ?? 'root',
            'password' => $configs['db_pusat_password'] ?? '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ]);

        DB::purge('db_pusat_push');
        $db = DB::connection('db_pusat_push');

        $username = $this->customer->username;
        $password = $this->customer->getDecryptedPassword();

        if ($this->action === 'delete') {
            $db->table('radcheck')->where('username', $username)->delete();
            $db->table('radusergroup')->where('username', $username)->delete();
            return;
        }

        // Create or Update
        // Update radcheck (Cleartext-Password)
        $db->table('radcheck')->updateOrInsert(
            ['username' => $username, 'attribute' => 'Cleartext-Password'],
            ['op' => ':=', 'value' => $password]
        );

        // Update radusergroup
        // Delete existing profiles first
        $db->table('radusergroup')->where('username', $username)->delete();
        $db->table('radusergroup')->insert([
            'username' => $username,
            'groupname' => $profile,
            'priority' => 1
        ]);
    }
}
