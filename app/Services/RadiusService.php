<?php

namespace App\Services;

use App\Models\Server;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * RadiusService — Manage RADIUS menggunakan Upluk Upluk RADIUS API (radius-ui backend)
 */
class RadiusService
{
    protected ?Server $currentServer = null;
    protected ?string $baseUrl = null;
    protected ?string $token = null;

    /**
     * Set server target.
     */
    public function connectTo(Server $server): self
    {
        $this->currentServer = $server;
        
        $this->baseUrl = rtrim($server->api_endpoint ?? $server->host, '/');
        if (!str_starts_with($this->baseUrl, 'http')) {
            $this->baseUrl = 'http://' . $this->baseUrl;
        }
        $this->token = $server->getDecryptedApiToken();

        return $this;
    }

    /**
     * Test koneksi ke Upluk Upluk RADIUS API.
     */
    public function testConnection(Server $server): array
    {
        try {
            $this->connectTo($server);
            
            $response = Http::withToken($this->token)
                ->timeout(5)
                ->get("{$this->baseUrl}/api/users");
            
            $configIdentity = '';
            try {
                $configResponse = Http::withToken($this->token)->timeout(5)->get("{$this->baseUrl}/api/config/server");
                if ($configResponse->successful()) {
                    $configIdentity = $configResponse->json('SERVER_IDENTITY') ?? '';
                }
            } catch (\Throwable $e) {}

            if ($response->successful()) {
                $users = $response->json();
                $msg = "Koneksi API berhasil ke ";
                return [
                    'status' => 'success',
                    'message' => $msg,
                    'identity' => $configIdentity ?: $this->baseUrl,
                    'has_radcheck' => true,
                    'user_count' => count($users),
                ];
            }

            return [
                'status' => 'error',
                'message' => "Gagal konek: API merespons dengan HTTP " . $response->status(),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => "Gagal konek: {$e->getMessage()}",
            ];
        }
    }

    // ===================================================
    // USER MANAGEMENT
    // ===================================================

    public function createUser(string $username, string $password, ?string $group = null): bool
    {
        try {
            $response = Http::withToken($this->token)->post("{$this->baseUrl}/api/users", [
                'username' => $username,
                'password' => $password,
                'profile' => $group
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("RadiusService::createUser failed: {$e->getMessage()}", [
                'username' => $username,
                'server' => $this->currentServer?->name,
            ]);
            return false;
        }
    }

    public function updateUser(string $username, ?string $newPassword = null, ?string $newGroup = null): bool
    {
        try {
            $response = Http::withToken($this->token)->post("{$this->baseUrl}/api/users/batch", [
                'users' => [
                    [
                        'username' => $username,
                        'password' => $newPassword,
                        'profile' => $newGroup
                    ]
                ]
            ]);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("RadiusService::updateUser failed: {$e->getMessage()}");
            return false;
        }
    }

    public function deleteUser(string $username): bool
    {
        try {
            $response = Http::withToken($this->token)->delete("{$this->baseUrl}/api/users/batch", [
                'usernames' => [$username]
            ]);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("RadiusService::deleteUser failed: {$e->getMessage()}");
            return false;
        }
    }

    public function getConfig(): array
    {
        try {
            $response = Http::withToken($this->token)->get("{$this->baseUrl}/api/config/server");
            if ($response->successful()) {
                return $response->json();
            }
            return [];
        } catch (\Throwable $e) {
            Log::error("RadiusService::getConfig failed: {$e->getMessage()}");
            return [];
        }
    }

    public function createProfile(array $data): bool
    {
        try {
            $response = Http::withToken($this->token)->post("{$this->baseUrl}/api/profiles", $data);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("RadiusService::createProfile failed: {$e->getMessage()}");
            return false;
        }
    }

    public function updateProfile(int $id, array $data): bool
    {
        try {
            $response = Http::withToken($this->token)->put("{$this->baseUrl}/api/profiles/{$id}", $data);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("RadiusService::updateProfile failed: {$e->getMessage()}");
            return false;
        }
    }

    public function deleteProfile(int $id): bool
    {
        try {
            $response = Http::withToken($this->token)->delete("{$this->baseUrl}/api/profiles/{$id}");
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("RadiusService::deleteProfile failed: {$e->getMessage()}");
            return false;
        }
    }

    // ===================================================
    // ISOLIR / UNISOLIR
    // ===================================================

    public function disableUser(string $username): bool
    {
        try {
            $response = Http::withToken($this->token)->post("{$this->baseUrl}/api/users/{$username}/disable");
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("RadiusService::disableUser failed: {$e->getMessage()}");
            return false;
        }
    }

    public function enableUser(string $username, ?string $password = null, ?string $group = null): bool
    {
        try {
            $payload = [];
            if ($password) $payload['password'] = $password;
            if ($group) $payload['profile'] = $group;

            $response = Http::withToken($this->token)->post("{$this->baseUrl}/api/users/{$username}/enable", $payload);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("RadiusService::enableUser failed: {$e->getMessage()}");
            return false;
        }
    }

    // ===================================================
    // MONITORING (READ-ONLY)
    // ===================================================

    public function getActiveSessions(): Collection
    {
        try {
            $response = Http::withToken($this->token)->get("{$this->baseUrl}/api/sessions");
            if ($response->successful()) {
                return collect($response->json());
            }
            return collect();
        } catch (\Throwable $e) {
            Log::error("RadiusService::getActiveSessions failed: {$e->getMessage()}");
            return collect();
        }
    }

    public function getUserSessions(string $username, int $limit = 50): Collection
    {
        try {
            // API saat ini hanya mengembalikan sesi aktif (acctstoptime IS NULL).
            // Kita filter berdasarkan username.
            $response = Http::withToken($this->token)->get("{$this->baseUrl}/api/sessions");
            if ($response->successful()) {
                return collect($response->json())
                    ->where('username', $username)
                    ->take($limit)
                    ->values();
            }
            return collect();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public function listUsers(): Collection
    {
        try {
            $response = Http::withToken($this->token)->get("{$this->baseUrl}/api/users");
            if ($response->successful()) {
                // API mengembalikan array of {username, password, profile}
                // Map menjadi object agar mirip dengan struktur Eloquent Collection sebelumnya
                return collect($response->json())->map(function ($item) {
                    return (object) $item;
                });
            }
            return collect();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public function listGroups(): Collection
    {
        try {
            $response = Http::withToken($this->token)->get("{$this->baseUrl}/api/profiles");
            if ($response->successful()) {
                return collect($response->json())->map(function ($item) {
                    return (object) $item;
                });
            }
            return collect();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    // ===================================================
    // BULK SYNC
    // ===================================================

    public function syncCustomersToRadius(Collection $customers): array
    {
        $synced = 0;
        $errors = 0;

        // Kita bisa menggunakan batch API jika radius-ui memiliki /api/users/batch
        $usersToSync = [];

        foreach ($customers as $customer) {
            if (!$customer->username) continue;

            $group = $customer->package?->radius_group;
            $password = $customer->pppoe_password ?? $customer->username;

            if ($customer->is_isolated) {
                // Handle isolir individually since batch may not support disable
                try {
                    $this->disableUser($customer->username);
                    $synced++;
                } catch (\Throwable $e) {
                    $errors++;
                }
            } else {
                $usersToSync[] = [
                    'username' => $customer->username,
                    'password' => $password,
                    'profile' => $group
                ];
            }
        }

        if (count($usersToSync) > 0) {
            try {
                $response = Http::withToken($this->token)->post("{$this->baseUrl}/api/users/batch", [
                    'users' => $usersToSync
                ]);
                
                if ($response->successful()) {
                    $synced += count($usersToSync);
                } else {
                    $errors += count($usersToSync);
                }
            } catch (\Throwable $e) {
                $errors += count($usersToSync);
                Log::warning("Batch sync failed: {$e->getMessage()}");
            }
        }

        return [
            'synced' => $synced,
            'errors' => $errors,
            'total' => $customers->count(),
        ];
    }
}
