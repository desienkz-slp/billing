<?php

namespace App\Services;

use App\Models\Radius\RadAcct;
use App\Models\Radius\RadCheck;
use App\Models\Radius\RadGroupReply;
use App\Models\Radius\RadReply;
use App\Models\Radius\RadUserGroup;
use App\Models\Server;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * RadiusService — Manage FreeRADIUS database secara dinamis per server.
 *
 * Setiap tenant bisa punya banyak server RADIUS. Service ini:
 * 1. Set koneksi MySQL dinamis ke server RADIUS target
 * 2. CRUD radcheck/radreply/radusergroup untuk manage user PPPoE
 * 3. Read radacct untuk monitoring session (read-only)
 * 4. Bulk sync dari billing ke RADIUS
 */
class RadiusService
{
    protected ?Server $currentServer = null;

    /**
     * Set koneksi database RADIUS ke server tertentu.
     * Harus dipanggil sebelum operasi RADIUS lainnya.
     */
    public function connectTo(Server $server): self
    {
        Config::set('database.connections.radius', [
            'driver'    => 'mysql',
            'host'      => $server->host,
            'port'      => $server->port ?? 3306,
            'database'  => $server->db_name ?? 'radius',
            'username'  => $server->username,
            'password'  => $server->getDecryptedDbPassword(),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
        ]);

        // Purge cached connection sehingga Laravel buat koneksi baru
        DB::purge('radius');

        $this->currentServer = $server;

        return $this;
    }

    /**
     * Test apakah koneksi ke RADIUS server berhasil.
     */
    public function testConnection(Server $server): array
    {
        try {
            $this->connectTo($server);
            DB::connection('radius')->getPdo();

            // Cek apakah tabel radcheck exists
            $tables = DB::connection('radius')
                ->select("SHOW TABLES LIKE 'radcheck'");

            $hasRadcheck = count($tables) > 0;

            // Count existing users
            $userCount = $hasRadcheck
                ? DB::connection('radius')->table('radcheck')->count()
                : 0;

            return [
                'status' => 'success',
                'message' => "Koneksi berhasil ke {$server->host}:{$server->port}/{$server->db_name}",
                'has_radcheck' => $hasRadcheck,
                'user_count' => $userCount,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => "Gagal konek: {$e->getMessage()}",
            ];
        }
    }

    // ===================================================
    // USER MANAGEMENT (radcheck + radusergroup)
    // ===================================================

    /**
     * Buat user baru di RADIUS.
     * Insert ke radcheck (password) dan radusergroup (group/paket).
     */
    public function createUser(string $username, string $password, ?string $group = null): bool
    {
        try {
            DB::connection('radius')->transaction(function () use ($username, $password, $group) {
                // Insert password ke radcheck
                RadCheck::on('radius')->updateOrCreate(
                    ['username' => $username, 'attribute' => 'Cleartext-Password'],
                    ['op' => ':=', 'value' => $password]
                );

                // Assign ke group jika ada
                if ($group) {
                    RadUserGroup::on('radius')->updateOrCreate(
                        ['username' => $username],
                        ['groupname' => $group, 'priority' => 1]
                    );
                }
            });

            return true;
        } catch (\Throwable $e) {
            Log::error("RadiusService::createUser failed: {$e->getMessage()}", [
                'username' => $username,
                'server' => $this->currentServer?->host,
            ]);
            return false;
        }
    }

    /**
     * Update password user di RADIUS.
     */
    public function updatePassword(string $username, string $newPassword): bool
    {
        try {
            $updated = RadCheck::on('radius')
                ->where('username', $username)
                ->where('attribute', 'Cleartext-Password')
                ->update(['value' => $newPassword]);

            return $updated > 0;
        } catch (\Throwable $e) {
            Log::error("RadiusService::updatePassword failed: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Hapus user dari RADIUS (semua entry di radcheck, radreply, radusergroup).
     */
    public function deleteUser(string $username): bool
    {
        try {
            DB::connection('radius')->transaction(function () use ($username) {
                RadCheck::on('radius')->where('username', $username)->delete();
                RadReply::on('radius')->where('username', $username)->delete();
                RadUserGroup::on('radius')->where('username', $username)->delete();
            });

            return true;
        } catch (\Throwable $e) {
            Log::error("RadiusService::deleteUser failed: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Ganti group user (ganti paket).
     */
    public function changeGroup(string $username, string $newGroup): bool
    {
        try {
            RadUserGroup::on('radius')->updateOrCreate(
                ['username' => $username],
                ['groupname' => $newGroup, 'priority' => 1]
            );

            return true;
        } catch (\Throwable $e) {
            Log::error("RadiusService::changeGroup failed: {$e->getMessage()}");
            return false;
        }
    }

    // ===================================================
    // ISOLIR / UNISOLIR
    // ===================================================

    /**
     * Disable user di RADIUS (isolir).
     * Menghapus entry dari radcheck sehingga auth gagal.
     */
    public function disableUser(string $username): bool
    {
        try {
            // Simpan password saat ini ke atribut khusus sebelum hapus
            $current = RadCheck::on('radius')
                ->where('username', $username)
                ->where('attribute', 'Cleartext-Password')
                ->first();

            if ($current) {
                // Backup password ke atribut non-auth
                RadCheck::on('radius')->updateOrCreate(
                    ['username' => $username, 'attribute' => 'Backup-Password'],
                    ['op' => ':=', 'value' => $current->value]
                );

                // Set Auth-Type ke Reject
                RadCheck::on('radius')->updateOrCreate(
                    ['username' => $username, 'attribute' => 'Auth-Type'],
                    ['op' => ':=', 'value' => 'Reject']
                );
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("RadiusService::disableUser failed: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Enable user di RADIUS (unisolir).
     * Restore password dari backup.
     */
    public function enableUser(string $username, ?string $password = null, ?string $group = null): bool
    {
        try {
            DB::connection('radius')->transaction(function () use ($username, $password, $group) {
                // Coba restore dari backup
                if (!$password) {
                    $backup = RadCheck::on('radius')
                        ->where('username', $username)
                        ->where('attribute', 'Backup-Password')
                        ->first();
                    $password = $backup?->value;
                }

                if ($password) {
                    // Restore password
                    RadCheck::on('radius')->updateOrCreate(
                        ['username' => $username, 'attribute' => 'Cleartext-Password'],
                        ['op' => ':=', 'value' => $password]
                    );
                }

                // Hapus Auth-Type Reject
                RadCheck::on('radius')
                    ->where('username', $username)
                    ->where('attribute', 'Auth-Type')
                    ->delete();

                // Hapus backup password
                RadCheck::on('radius')
                    ->where('username', $username)
                    ->where('attribute', 'Backup-Password')
                    ->delete();

                // Restore group jika diberikan
                if ($group) {
                    RadUserGroup::on('radius')->updateOrCreate(
                        ['username' => $username],
                        ['groupname' => $group, 'priority' => 1]
                    );
                }
            });

            return true;
        } catch (\Throwable $e) {
            Log::error("RadiusService::enableUser failed: {$e->getMessage()}");
            return false;
        }
    }

    // ===================================================
    // MONITORING (READ-ONLY)
    // ===================================================

    /**
     * Get active sessions dari radacct (belum disconnect).
     */
    public function getActiveSessions(): Collection
    {
        try {
            return RadAcct::on('radius')
                ->whereNull('acctstoptime')
                ->orderByDesc('acctstarttime')
                ->get();
        } catch (\Throwable $e) {
            Log::error("RadiusService::getActiveSessions failed: {$e->getMessage()}");
            return collect();
        }
    }

    /**
     * Get session history untuk user tertentu.
     */
    public function getUserSessions(string $username, int $limit = 50): Collection
    {
        try {
            return RadAcct::on('radius')
                ->where('username', $username)
                ->orderByDesc('acctstarttime')
                ->limit($limit)
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * Get semua user yang terdaftar di radcheck.
     */
    public function listUsers(): Collection
    {
        try {
            return RadCheck::on('radius')
                ->where('radcheck.attribute', 'Cleartext-Password')
                ->leftJoin('radusergroup', 'radcheck.username', '=', 'radusergroup.username')
                ->select('radcheck.username', 'radcheck.value as password', 'radusergroup.groupname as profile')
                ->orderBy('radcheck.username')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * Get semua group dan atributnya.
     */
    public function listGroups(): Collection
    {
        try {
            return RadGroupReply::on('radius')
                ->orderBy('groupname')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    // ===================================================
    // BULK SYNC
    // ===================================================

    /**
     * Sync customers dari billing ke RADIUS database.
     * Hanya sync customer yang aktif dan punya username.
     */
    public function syncCustomersToRadius(Collection $customers): array
    {
        $synced = 0;
        $errors = 0;

        foreach ($customers as $customer) {
            if (!$customer->username) continue;

            $group = $customer->package?->radius_group;
            $password = $customer->pppoe_password ?? $customer->username;

            try {
                if ($customer->is_isolated) {
                    $this->disableUser($customer->username);
                } else {
                    $this->createUser($customer->username, $password, $group);
                }
                $synced++;
            } catch (\Throwable $e) {
                $errors++;
                Log::warning("Sync failed for {$customer->username}: {$e->getMessage()}");
            }
        }

        return [
            'synced' => $synced,
            'errors' => $errors,
            'total' => $customers->count(),
        ];
    }
}
