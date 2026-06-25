<?php

namespace App\Services\MikroTik;

use App\Models\Router;

class MikroTikService
{
    /**
     * Connect to a MikroTik router, returns API instance or null on failure.
     */
    public function connect(Router $router): ?\RouterosAPI
    {
        $apiFile = __DIR__ . '/RouterosAPI.php';
        if (!file_exists($apiFile)) {
            return null;
        }
        require_once $apiFile;

        $API = new \RouterosAPI();
        $API->port = $router->port;
        $API->timeout = 15;
        $API->attempts = 1;

        if (!$API->connect($router->host, $router->username, $router->getDecryptedPassword())) {
            return null;
        }

        return $API;
    }

    /**
     * Get system identity.
     */
    public function getIdentity(Router $router): ?string
    {
        $API = $this->connect($router);
        if (!$API) return null;

        try {
            $res = $API->comm('/system/identity/print');
            return is_array($res) && isset($res[0]['name']) ? $res[0]['name'] : null;
        } catch (\Throwable $e) {
            return null;
        } finally {
            $API->disconnect();
        }
    }

    /**
     * Get system resources (CPU, RAM, uptime, version, etc.)
     */
    public function getSystemResources(Router $router): ?array
    {
        $API = $this->connect($router);
        if (!$API) return null;

        try {
            $res = $API->comm('/system/resource/print');
            return is_array($res) && isset($res[0]) ? $res[0] : null;
        } finally {
            $API->disconnect();
        }
    }

    /**
     * Get all PPPoE secrets from router.
     */
    public function getPppoeSecrets(Router $router): array
    {
        $API = $this->connect($router);
        if (!$API) return [];

        try {
            $res = $API->comm('/ppp/secret/print');
            if (!is_array($res) || isset($res['!trap'])) return [];
            return array_filter($res, 'is_array');
        } finally {
            $API->disconnect();
        }
    }

    /**
     * Get all PPPoE profiles from router.
     */
    public function getPppoeProfiles(Router $router): array
    {
        $API = $this->connect($router);
        if (!$API) return [];

        try {
            $res = $API->comm('/ppp/profile/print');
            if (!is_array($res) || isset($res['!trap'])) return [];
            return array_filter($res, 'is_array');
        } finally {
            $API->disconnect();
        }
    }

    /**
     * Get active PPP connections.
     */
    public function getActiveConnections(Router $router): array
    {
        $API = $this->connect($router);
        if (!$API) return [];

        try {
            $res = $API->comm('/ppp/active/print');
            if (!is_array($res) || isset($res['!trap'])) return [];
            return array_filter($res, 'is_array');
        } finally {
            $API->disconnect();
        }
    }

    /**
     * Get PPPoE server interfaces (useful for finding which physical interface a user is on).
     */
    public function getPppoeServerInterfaces(Router $router): array
    {
        $API = $this->connect($router);
        if (!$API) return [];

        try {
            $res = $API->comm('/interface/pppoe-server/print');
            if (!is_array($res) || isset($res['!trap'])) return [];
            return array_filter($res, 'is_array');
        } finally {
            $API->disconnect();
        }
    }

    /**
     * Disable a PPPoE secret (isolir).
     */
    public function disableSecret(Router $router, string $secretName): bool
    {
        $API = $this->connect($router);
        if (!$API) return false;

        try {
            // Find the secret by name
            $secrets = $API->comm('/ppp/secret/print', ['?name' => $secretName]);
            if (empty($secrets) || !isset($secrets[0]['.id'])) return false;

            $id = $secrets[0]['.id'];
            $API->comm('/ppp/secret/set', ['=.id' => $id, '=disabled' => 'yes']);

            // Also disconnect active session
            $this->disconnectActiveByName($API, $secretName);

            return true;
        } catch (\Throwable $e) {
            return false;
        } finally {
            $API->disconnect();
        }
    }

    /**
     * Remove a PPPoE secret.
     */
    public function removeSecret(Router $router, string $secretName): bool
    {
        $API = $this->connect($router);
        if (!$API) return false;

        try {
            $secrets = $API->comm('/ppp/secret/print', ['?name' => $secretName]);
            if (empty($secrets) || !isset($secrets[0]['.id'])) return false;

            $id = $secrets[0]['.id'];
            $API->comm('/ppp/secret/remove', ['=.id' => $id]);

            // Also disconnect active session
            $this->disconnectActiveByName($API, $secretName);

            return true;
        } catch (\Throwable $e) {
            return false;
        } finally {
            $API->disconnect();
        }
    }

    /**
     * Enable a PPPoE secret (unisolir).
     */
    public function enableSecret(Router $router, string $secretName): bool
    {
        $API = $this->connect($router);
        if (!$API) return false;

        try {
            $secrets = $API->comm('/ppp/secret/print', ['?name' => $secretName]);
            if (empty($secrets) || !isset($secrets[0]['.id'])) return false;

            $id = $secrets[0]['.id'];
            $API->comm('/ppp/secret/set', ['=.id' => $id, '=disabled' => 'no']);

            return true;
        } catch (\Throwable $e) {
            return false;
        } finally {
            $API->disconnect();
        }
    }


    /**
     * Change profile of a PPPoE secret by username (For Isolir).
     */
    public function changeSecretProfile(Router $router, string $username, string $profileName): bool
    {
        $API = $this->connect($router);
        if (!$API) return false;

        try {
            $secrets = $API->comm('/ppp/secret/print', ['?name' => $username]);
            if (!empty($secrets) && isset($secrets[0]['.id'])) {
                $API->comm('/ppp/secret/set', [
                    '=.id' => $secrets[0]['.id'],
                    '=profile' => $profileName
                ]);
            }
            $this->disconnectActiveByName($API, $username);
            return true;
        } catch (\Throwable $e) {
            return false;
        } finally {
            $API->disconnect();
        }
    }

    /**
     * Disconnect an active PPP session by name.
     */
    private function disconnectActiveByName(\RouterosAPI $API, string $name): void
    {
        try {
            $active = $API->comm('/ppp/active/print', ['?name' => $name]);
            if (!empty($active) && isset($active[0]['.id'])) {
                $API->comm('/ppp/active/remove', ['=.id' => $active[0]['.id']]);
            }
        } catch (\Throwable $e) {
            // Non-critical
        }
    }

    /**
     * Kick an active PPP session directly (useful for RADIUS users).
     */
    public function kickActiveSession(Router $router, string $username): bool
    {
        $API = $this->connect($router);
        if (!$API) return false;

        try {
            $this->disconnectActiveByName($API, $username);
            return true;
        } catch (\Throwable $e) {
            return false;
        } finally {
            $API->disconnect();
        }
    }

    /**
     * Check if a router is reachable.
     */
    public function ping(Router $router): bool
    {
        $API = $this->connect($router);
        if (!$API) return false;
        $API->disconnect();
        return true;
    }
}
