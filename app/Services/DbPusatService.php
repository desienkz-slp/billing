<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class DbPusatService
{
    private function getConfigs(): array
    {
        return DB::table('billing_configs')
            ->where('tenant_id', session('tenant_id', auth()->user()?->tenant_id))
            ->whereIn('key', [
                'db_pusat_software', 'db_pusat_type', 'db_pusat_sync_method', 
                'db_pusat_auth_type', 'db_pusat_url', 'db_pusat_token', 
                'db_pusat_username', 'db_pusat_password'
            ])
            ->pluck('value', 'key')->toArray();
    }

    public function pullPackages(): void
    {
        $configs = $this->getConfigs();
        if (empty($configs)) return;

        $type = $configs['db_pusat_type'] ?? 'api';
        $profiles = [];

        try {
            if ($type === 'api') {
                $profiles = $this->pullFromApi($configs);
            } else {
                $profiles = $this->pullFromDatabase($configs, $type);
            }

            $tenantId = session('tenant_id', auth()->user()?->tenant_id);
            if (!$tenantId) return;

            // Save profiles to db_pusat_profiles table
            foreach ($profiles as $profile) {
                DB::table('db_pusat_profiles')->updateOrInsert(
                    ['tenant_id' => $tenantId, 'name' => $profile],
                    ['description' => "Pulled from DB Pusat (" . ($configs['db_pusat_software'] ?? '') . ")"]
                );
            }

        } catch (\Exception $e) {
            Log::error("DbPusatService pullPackages failed: " . $e->getMessage());
        }
    }

    private function pullFromApi(array $configs): array
    {
        $url = $configs['db_pusat_url'] ?? '';
        if (!$url) return [];

        $authType = $configs['db_pusat_auth_type'] ?? 'token';
        
        // This is a generic implementation. Specific API structures vary by software.
        // Assuming the API returns a JSON list of profiles.
        $request = Http::timeout(15);
        if ($authType === 'token') {
            $request->withToken($configs['db_pusat_token'] ?? '');
        } else {
            $request->withBasicAuth($configs['db_pusat_username'] ?? '', $configs['db_pusat_password'] ?? '');
        }

        $response = $request->get(rtrim($url, '/') . '/profiles'); // Mock endpoint
        
        if ($response->successful()) {
            $data = $response->json();
            // Assuming data is array of profile strings or objects with 'name'
            $profiles = [];
            foreach ($data as $item) {
                if (is_string($item)) {
                    $profiles[] = $item;
                } elseif (is_array($item) && isset($item['name'])) {
                    $profiles[] = $item['name'];
                } elseif (is_array($item) && isset($item['profile'])) {
                    $profiles[] = $item['profile'];
                }
            }
            return $profiles;
        }

        return [];
    }

    private function pullFromDatabase(array $configs, string $driver): array
    {
        // Parse host from URL field (remove http:// etc if user put it)
        $host = str_replace(['http://', 'https://'], '', $configs['db_pusat_url'] ?? '127.0.0.1');
        $host = explode('/', $host)[0];
        $host = explode(':', $host)[0];

        Config::set('database.connections.db_pusat', [
            'driver' => $driver,
            'host' => $host,
            'port' => $driver === 'pgsql' ? '5432' : '3306',
            'database' => 'radius', // Standard freeradius db name
            'username' => $configs['db_pusat_username'] ?? 'root',
            'password' => $configs['db_pusat_password'] ?? '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ]);

        DB::purge('db_pusat');

        // Usually profiles in freeradius are stored in radgroupreply
        $groups = DB::connection('db_pusat')->table('radgroupreply')
                    ->select('groupname')
                    ->distinct()
                    ->pluck('groupname')
                    ->toArray();

        return $groups;
    }
}
