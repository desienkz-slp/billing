<?php

namespace App\Services\MikroTik;

use App\Models\Router;
use App\Models\RouterConfigBackup;

class RouterConfigService
{
    /**
     * Fetch config from MikroTik router.
     * Uses multiple strategies: API export, FTP download, raw read.
     */
    public function fetchConfig(Router $router): string
    {
        $apiFile = __DIR__ . '/RouterosAPI.php';
        if (!file_exists($apiFile)) {
            throw new \RuntimeException('RouterOS API library tidak ditemukan.');
        }
        require_once $apiFile;

        $host = $router->host;
        $port = $router->port;
        $ftpPort = $router->ftp_port ?? 21;
        $username = $router->username;
        $password = $router->getDecryptedPassword();

        $lines = [];

        // ─── Strategy 1: API /export via comm() ───
        $lines = $this->tryApiExport($host, $port, $username, $password);

        // ─── Strategy 2: FTP — export to file, download via FTP ───
        if (empty($lines) && function_exists('ftp_connect')) {
            $lines = $this->tryFtpExport($host, $port, $ftpPort, $username, $password);
        }

        // ─── Strategy 3: API comm('/export') again + raw read ───
        if (empty($lines)) {
            $lines = $this->tryRawApiExport($host, $port, $username, $password);
        }

        if (empty($lines)) {
            throw new \RuntimeException('Tidak dapat mengambil config dari router. Semua metode export gagal.');
        }

        return implode("\n", $lines);
    }

    /**
     * Strategy 1: Use API comm('/export') and parse response
     */
    private function tryApiExport(string $host, int $port, string $user, string $pass): array
    {
        $lines = [];

        try {
            $API = new \RouterosAPI();
            $API->port = $port;
            $API->timeout = 30;
            $API->attempts = 1;

            if (!$API->connect($host, $user, $pass)) {
                return [];
            }

            try {
                // comm() auto-parses — /export returns parsed data
                $parsed = $API->comm('/export');

                if (is_array($parsed)) {
                    // Check for !re format
                    if (isset($parsed['!re']) && is_array($parsed['!re'])) {
                        foreach ($parsed['!re'] as $row) {
                            if (is_array($row) && isset($row['ret'])) {
                                $lines[] = $row['ret'];
                            }
                        }
                    } else {
                        foreach ($parsed as $row) {
                            if (is_array($row) && isset($row['ret'])) {
                                $lines[] = $row['ret'];
                            } elseif (is_string($row)) {
                                $lines[] = $row;
                            }
                        }
                    }
                } elseif (is_string($parsed)) {
                    $lines[] = $parsed;
                }

                // If parsed approach got nothing, try raw write/read
                if (empty($lines)) {
                    $API->write('/export', true);
                    $raw = $API->read(false);
                    foreach ((array) $raw as $item) {
                        if (is_string($item)) {
                            if (strpos($item, '=ret=') === 0) {
                                $lines[] = substr($item, 5);
                            } elseif (preg_match('/^=ret=(.*)/s', $item, $m)) {
                                $lines[] = trim($m[1]);
                            } elseif (preg_match('/^ret=(.*)$/s', $item, $m)) {
                                $lines[] = trim($m[1]);
                            }
                        }
                    }
                }
            } finally {
                $API->disconnect();
            }
        } catch (\Throwable $e) {
            // Strategy failed, continue to next
        }

        return $lines;
    }

    /**
     * Strategy 2: Export to file on router, download via FTP
     */
    private function tryFtpExport(string $host, int $apiPort, int $ftpPort, string $user, string $pass): array
    {
        $lines = [];
        $fname = 'laravel_cfg_' . time();
        $remoteRsc = $fname . '.rsc';

        try {
            // Step 1: Tell router to export to file via API
            $API = new \RouterosAPI();
            $API->port = $apiPort;
            $API->timeout = 15;
            $API->attempts = 1;

            if ($API->connect($host, $user, $pass)) {
                try {
                    $API->comm('/export', ['file' => $fname]);
                    usleep(1500000); // 1.5s wait for file to be written
                } finally {
                    $API->disconnect();
                }
            } else {
                return [];
            }

            // Step 2: Download via FTP stream wrapper
            $content = null;
            $encUser = rawurlencode($user);
            $encPass = rawurlencode($pass);
            $streamUrl = "ftp://{$encUser}:{$encPass}@{$host}:{$ftpPort}/{$remoteRsc}";
            $ctx = stream_context_create(['ftp' => ['request_fulluri' => true, 'overwrite' => true]]);
            $content = @file_get_contents($streamUrl, false, $ctx);

            // Step 3: If stream wrapper fails, try ftp_* functions
            if (($content === false || strlen(trim((string) $content)) < 10) && function_exists('ftp_connect')) {
                $ftp = @ftp_connect($host, $ftpPort, 10);
                if ($ftp && @ftp_login($ftp, $user, $pass)) {
                    @ftp_set_option($ftp, FTP_TIMEOUT_SEC, 20);
                    foreach ([true, false] as $pasv) {
                        @ftp_pasv($ftp, $pasv);
                        $tmp = tempnam(sys_get_temp_dir(), 'mtk_');
                        $paths = [$remoteRsc, './' . $remoteRsc, 'disk/' . $remoteRsc, 'flash/' . $remoteRsc];
                        foreach ($paths as $p) {
                            if (@ftp_get($ftp, $tmp, $p, FTP_ASCII)) {
                                $content = @file_get_contents($tmp);
                                break 2;
                            }
                        }
                        if ($content !== null && $content !== false) break;

                        // Try listing files
                        $list = @ftp_nlist($ftp, '.') ?: @ftp_nlist($ftp, '');
                        if (is_array($list)) {
                            foreach ($list as $f) {
                                $bn = basename($f);
                                if ($bn === $remoteRsc || stripos($bn, $fname) !== false) {
                                    if (@ftp_get($ftp, $tmp, $f, FTP_ASCII)) {
                                        $content = @file_get_contents($tmp);
                                        break 2;
                                    }
                                }
                            }
                        }
                        @unlink($tmp);
                    }
                    @ftp_delete($ftp, $remoteRsc);
                    ftp_close($ftp);
                }
            }

            if ($content !== null && $content !== false && strlen(trim($content)) > 0) {
                $lines = array_values(array_filter(array_map('trim', explode("\n", $content))));
            }

            // Step 4: Clean up file from router via API
            if (!empty($lines)) {
                try {
                    $API2 = new \RouterosAPI();
                    $API2->port = $apiPort;
                    $API2->timeout = 5;
                    $API2->attempts = 1;
                    if ($API2->connect($host, $user, $pass)) {
                        try {
                            @$API2->comm('/file/remove', ['=numbers' => $remoteRsc]);
                            @$API2->comm('/file/remove', ['=numbers' => $fname]);
                        } finally {
                            $API2->disconnect();
                        }
                    }
                } catch (\Throwable $e) {
                    // Cleanup failed, not critical
                }
            }
        } catch (\Throwable $e) {
            // Strategy failed
        }

        return $lines;
    }

    /**
     * Strategy 3: API raw write/read with different parsing
     */
    private function tryRawApiExport(string $host, int $port, string $user, string $pass): array
    {
        $lines = [];

        try {
            $API = new \RouterosAPI();
            $API->port = $port;
            $API->timeout = 30;
            $API->attempts = 1;

            if (!$API->connect($host, $user, $pass)) {
                return [];
            }

            try {
                $API->write('/export', true);
                $raw = $API->read(false);
                for ($i = 0; $i < count($raw); $i++) {
                    $item = $raw[$i];
                    if (is_string($item)) {
                        if (strpos($item, '=ret=') === 0) {
                            $lines[] = substr($item, 5);
                        } elseif (preg_match('/^=ret=(.*)/s', $item, $m)) {
                            $lines[] = trim($m[1]);
                        } elseif (preg_match('/^ret=(.*)$/s', $item, $m)) {
                            $lines[] = trim($m[1]);
                        } elseif ($item === 'ret' && isset($raw[$i + 1])) {
                            $lines[] = $raw[$i + 1];
                        }
                    }
                }
            } finally {
                $API->disconnect();
            }
        } catch (\Throwable $e) {
            // Strategy failed
        }

        return $lines;
    }

    /**
     * Fetch selective sections only (for backup)
     */
    public function fetchConfigSelective(Router $router): string
    {
        require_once __DIR__ . '/RouterosAPI.php';

        $host = $router->host;
        $port = $router->port;
        $user = $router->username;
        $pass = $router->getDecryptedPassword();

        $sections = [
            '/ip pool' => '/ip/pool/export',
            '/ppp profile' => '/ppp/profile/export',
            '/ppp secret' => '/ppp/secret/export',
            '/ppp pool' => '/ppp/pool/export',
            '/ip address' => '/ip/address/export',
            '/queue simple' => '/queue/simple/export',
        ];

        $API = new \RouterosAPI();
        $API->port = $port;
        $API->timeout = 30;
        $API->attempts = 1;

        if (!$API->connect($host, $user, $pass)) {
            // Fallback to full export
            return $this->fetchConfig($router);
        }

        $allLines = [];
        $hasData = false;

        try {
            foreach ($sections as $label => $path) {
                try {
                    $parsed = @$API->comm($path);
                    $lines = $this->extractLines($parsed);
                    if (!empty($lines)) {
                        $hasData = true;
                        $allLines[] = $label;
                        $allLines = array_merge($allLines, $lines);
                        $allLines[] = '';
                    }
                } catch (\Throwable $e) {
                    // Section failed, continue
                }
            }
        } finally {
            $API->disconnect();
        }

        if ($hasData && !empty($allLines)) {
            return implode("\n", $allLines);
        }

        // Fallback to full export then filter
        $full = $this->fetchConfig($router);
        $filtered = $this->filterConfigToSections($full);
        return $filtered !== '' ? $filtered : $full;
    }

    /**
     * Extract lines from parsed RouterOS API response
     */
    private function extractLines($parsed): array
    {
        $lines = [];
        if (is_array($parsed)) {
            if (isset($parsed['!re']) && is_array($parsed['!re'])) {
                foreach ($parsed['!re'] as $row) {
                    if (is_array($row) && isset($row['ret'])) $lines[] = $row['ret'];
                }
            } else {
                foreach ($parsed as $row) {
                    if (is_array($row) && isset($row['ret'])) $lines[] = $row['ret'];
                    elseif (is_string($row)) $lines[] = $row;
                }
            }
        } elseif (is_string($parsed)) {
            $lines[] = $parsed;
        }
        return $lines;
    }

    /**
     * Filter full export to only relevant sections
     */
    private function filterConfigToSections(string $fullConfig): string
    {
        $allowed = ['/ip pool', '/ppp profile', '/ppp secret', '/ppp pool', '/ip address', '/queue simple'];
        $lines = explode("\n", $fullConfig);
        $result = [];
        $current = null;

        foreach ($lines as $line) {
            $trimmed = rtrim($line);
            if (preg_match('/^(\/[a-z0-9\s\/\-]+)/', $trimmed, $m)) {
                $section = trim($m[1]);
                $current = null;
                foreach ($allowed as $a) {
                    if ($section === $a || strpos($section, $a . ' ') === 0) {
                        $current = $a;
                        break;
                    }
                }
            }
            if ($current !== null) {
                $result[] = $line;
            }
        }

        return implode("\n", $result);
    }

    /**
     * Create a backup entry in database
     */
    public function createBackup(Router $router, string $configText): RouterConfigBackup
    {
        $backup = RouterConfigBackup::create([
            'router_id' => $router->id,
            'config_name' => $router->name,
            'config_text' => $configText,
        ]);

        // Keep only last 3 backups per router
        $count = RouterConfigBackup::where('router_id', $router->id)->count();
        if ($count > 3) {
            $keepIds = RouterConfigBackup::where('router_id', $router->id)
                ->orderByDesc('created_at')
                ->limit(3)
                ->pluck('id');

            RouterConfigBackup::where('router_id', $router->id)
                ->whereNotIn('id', $keepIds)
                ->delete();
        }

        return $backup;
    }

    private function _extractLinesFromParsed($parsed): array
    {
        $lines = [];
        if (is_array($parsed)) {
            if (isset($parsed['!re']) && is_array($parsed['!re'])) {
                foreach ($parsed['!re'] as $row) {
                    if (is_array($row) && isset($row['ret'])) $lines[] = $row['ret'];
                }
            } else {
                foreach ($parsed as $row) {
                    if (is_array($row) && isset($row['ret'])) $lines[] = $row['ret'];
                    elseif (is_string($row)) $lines[] = $row;
                }
            }
        } elseif (is_string($parsed)) {
            $lines[] = $parsed;
        }
        return $lines;
    }

    private function _filterConfigToSections(string $fullConfig): string
    {
        $allowed = ['/ip pool', '/ppp profile', '/ppp secret', '/ppp pool', '/ip address', '/queue simple'];
        $lines = explode("\n", $fullConfig);
        $result = [];
        $current = null;
        foreach ($lines as $line) {
            $trimmed = rtrim($line);
            if (preg_match('/^(\/[a-z0-9\s\/\-]+)/', $trimmed, $m)) {
                $section = trim($m[1]);
                $current = null;
                foreach ($allowed as $a) {
                    if ($section === $a || strpos($section, $a . ' ') === 0) {
                        $current = $a;
                        break;
                    }
                }
            }
            if ($current !== null) {
                $result[] = $line;
            }
        }
        return implode("\n", $result);
    }

    public function fetchConfigFromRouterSelective(Router $router): string
    {
        // Ambil full config dengan show-sensitive via FTP, lalu filter section yang dibutuhkan
        $fullConfig = $this->_exportFullSensitiveViaFtp($router);

        if (!empty($fullConfig)) {
            $filtered = $this->_filterConfigToSections($fullConfig);
            return $filtered !== '' ? $filtered : $fullConfig;
        }

        // Fallback: full export tanpa FTP
        $full = $this->fetchConfigFromRouterFull($router);
        $filtered = $this->_filterConfigToSections($full);
        return $filtered !== '' ? $filtered : $full;
    }

    /**
     * Full export show-sensitive via API + FTP download.
     */
    private function _exportFullSensitiveViaFtp(Router $router): string
    {
        $apiFile = __DIR__ . '/RouterosAPI.php';
        require_once $apiFile;

        $pass = $router->getDecryptedPassword();
        $fname = 'lrv_cfg_' . time();
        $remoteRsc = $fname . '.rsc';

        // Step 1: Perintahkan router export show-sensitive ke file
        $API = new \RouterosAPI();
        $API->port = (int)$router->port;
        $API->timeout = 15;
        if (!$API->connect($router->host, $router->username, $pass)) {
            return '';
        }
        try {
            $API->comm('/export', ['file' => $fname, 'show-sensitive' => '']);
            usleep(2000000); // 2 detik tunggu file tertulis
        } finally {
            $API->disconnect();
        }

        // Step 2: Download file via FTP
        $content = null;
        $ftpPort = (int)($router->ftp_port ?? 21);

        // Coba stream wrapper dulu
        $encUser = rawurlencode($router->username);
        $encPass = rawurlencode($pass);
        $streamUrl = "ftp://{$encUser}:{$encPass}@{$router->host}:{$ftpPort}/{$remoteRsc}";
        $ctx = stream_context_create(['ftp' => ['request_fulluri' => true, 'overwrite' => true]]);
        $content = @file_get_contents($streamUrl, false, $ctx);

        // Fallback: ftp_connect
        if (($content === false || strlen(trim((string)$content)) < 10) && function_exists('ftp_connect')) {
            $ftp = @ftp_connect($router->host, $ftpPort, 10);
            if ($ftp && @ftp_login($ftp, $router->username, $pass)) {
                @ftp_set_option($ftp, FTP_TIMEOUT_SEC, 20);
                foreach ([true, false] as $pasv) {
                    @ftp_pasv($ftp, $pasv);
                    $tmp = tempnam(sys_get_temp_dir(), 'mtk_');
                    if (@ftp_get($ftp, $tmp, $remoteRsc, FTP_ASCII)) {
                        $content = @file_get_contents($tmp);
                        @unlink($tmp);
                        if ($content !== false && strlen(trim($content)) > 10) {
                            break;
                        }
                    }
                }
                @ftp_close($ftp);
            }
        }

        // Step 3: Hapus file dari router
        $API2 = new \RouterosAPI();
        $API2->port = (int)$router->port;
        $API2->timeout = 5;
        if ($API2->connect($router->host, $router->username, $pass)) {
            try {
                @$API2->comm('/file/remove', ['=numbers' => $remoteRsc]);
                @$API2->comm('/file/remove', ['=numbers' => $fname]);
            } finally {
                $API2->disconnect();
            }
        }

        return (string)$content;
    }

    /**
     * Ambil bagian /ppp secret dari full export show-sensitive via FTP.
     */
    private function _fetchPppSecretSensitive(Router $router): array
    {
        $apiFile = __DIR__ . '/RouterosAPI.php';
        require_once $apiFile;

        $pass = $router->getDecryptedPassword();
        $fname = 'lrv_secret_' . time();
        $remoteRsc = $fname . '.rsc';

        // Step 1: Perintahkan router export show-sensitive ke file
        $API = new \RouterosAPI();
        $API->port = (int)$router->port;
        $API->timeout = 15;
        if (!$API->connect($router->host, $router->username, $pass)) {
            return [];
        }
        try {
            $API->comm('/export', ['file' => $fname, 'show-sensitive' => '']);
            usleep(2000000); // 2 detik tunggu file tertulis
        } finally {
            $API->disconnect();
        }

        // Step 2: Download file via FTP
        $content = null;
        $ftpPort = (int)($router->ftp_port ?? 21);

        // Coba stream wrapper dulu
        $encUser = rawurlencode($router->username);
        $encPass = rawurlencode($pass);
        $streamUrl = "ftp://{$encUser}:{$encPass}@{$router->host}:{$ftpPort}/{$remoteRsc}";
        $ctx = stream_context_create(['ftp' => ['request_fulluri' => true, 'overwrite' => true]]);
        $content = @file_get_contents($streamUrl, false, $ctx);

        // Fallback: ftp_connect
        if (($content === false || strlen(trim((string)$content)) < 10) && function_exists('ftp_connect')) {
            $ftp = @ftp_connect($router->host, $ftpPort, 10);
            if ($ftp && @ftp_login($ftp, $router->username, $pass)) {
                @ftp_set_option($ftp, FTP_TIMEOUT_SEC, 20);
                foreach ([true, false] as $pasv) {
                    @ftp_pasv($ftp, $pasv);
                    $tmp = tempnam(sys_get_temp_dir(), 'mtk_');
                    if (@ftp_get($ftp, $tmp, $remoteRsc, FTP_ASCII)) {
                        $content = @file_get_contents($tmp);
                        @unlink($tmp);
                        if ($content !== false && strlen(trim($content)) > 10) {
                            break;
                        }
                    }
                }
                @ftp_close($ftp);
            }
        }

        // Step 3: Hapus file dari router
        $API2 = new \RouterosAPI();
        $API2->port = (int)$router->port;
        $API2->timeout = 5;
        if ($API2->connect($router->host, $router->username, $pass)) {
            try {
                @$API2->comm('/file/remove', ['=numbers' => $remoteRsc]);
                @$API2->comm('/file/remove', ['=numbers' => $fname]);
            } finally {
                $API2->disconnect();
            }
        }

        if (empty($content)) {
            return [];
        }

        // Step 4: Filter hanya bagian /ppp secret dari full config
        $lines = explode("\n", $content);
        $result = [];
        $inSection = false;
        foreach ($lines as $line) {
            $trimmed = rtrim($line);
            // Deteksi awal section
            if (preg_match('/^\/ppp secret\b/', $trimmed)) {
                $inSection = true;
                continue; // skip header section-nya sendiri
            }
            // Deteksi section baru (keluar dari /ppp secret)
            if ($inSection && preg_match('/^\/[a-z]/', $trimmed)) {
                break;
            }
            if ($inSection && $trimmed !== '') {
                $result[] = $trimmed;
            }
        }
        return $result;
    }

    public function fetchConfigFromRouterFull(Router $router): string
    {
        $apiFile = __DIR__ . '/RouterosAPI.php';
        require_once $apiFile;

        $lines = [];
        $pass = $router->getDecryptedPassword();

        // Prioritas 1: API /export ke terminal
        $API = new \RouterosAPI();
        $API->port = (int)$router->port;
        $API->timeout = 30;
        if ($API->connect($router->host, $router->username, $pass)) {
            try {
                $parsed = $API->comm('/export');
                if (is_array($parsed)) {
                    if (isset($parsed['!re']) && is_array($parsed['!re'])) {
                        foreach ($parsed['!re'] as $row) {
                            if (is_array($row) && isset($row['ret'])) $lines[] = $row['ret'];
                        }
                    } else {
                        foreach ($parsed as $row) {
                            if (is_array($row) && isset($row['ret'])) $lines[] = $row['ret'];
                            elseif (is_string($row)) $lines[] = $row;
                        }
                    }
                } elseif (is_string($parsed)) {
                    $lines[] = $parsed;
                }
                if (empty($lines)) {
                    $API->write('/export', true);
                    $raw = $API->read(false);
                    foreach ((array)$raw as $item) {
                        if (is_string($item)) {
                            if (strpos($item, '=ret=') === 0) $lines[] = substr($item, 5);
                            elseif (preg_match('/^=ret=(.*)/s', $item, $m)) $lines[] = trim($m[1]);
                            elseif (preg_match('/^ret=(.*)$/s', $item, $m)) $lines[] = trim($m[1]);
                        }
                    }
                }
            } finally {
                $API->disconnect();
            }
        }

        // Prioritas 2: FTP - export ke file, unduh via FTP
        if (empty($lines) && function_exists('ftp_connect')) {
            $fname = 'php_cfg_' . time();
            $remoteRsc = $fname . '.rsc';
            $API = new \RouterosAPI();
            $API->port = (int)$router->port;
            $API->timeout = 15;
            if ($API->connect($router->host, $router->username, $pass)) {
                try {
                    $API->comm('/export', ['file' => $fname]);
                    usleep(1200000);
                } finally {
                    $API->disconnect();
                }
            }
            $content = null;
            $encUser = rawurlencode($router->username);
            $encPass = rawurlencode($pass);
            $ftpPort = (int)($router->ftp_port ?? 21);
            $streamUrl = "ftp://{$encUser}:{$encPass}@{$router->host}:{$ftpPort}/{$remoteRsc}";
            $ctx = stream_context_create(['ftp' => ['request_fulluri' => true, 'overwrite' => true]]);
            $content = @file_get_contents($streamUrl, false, $ctx);
            
            if (($content === false || strlen(trim((string)$content)) < 10) && function_exists('ftp_connect')) {
                $ftp = @ftp_connect($router->host, $ftpPort, 10);
                if ($ftp && @ftp_login($ftp, $router->username, $pass)) {
                    @ftp_set_option($ftp, FTP_TIMEOUT_SEC, 20);
                    foreach ([true, false] as $pasv) {
                        @ftp_pasv($ftp, $pasv);
                        $tmp = tempnam(sys_get_temp_dir(), 'mtk_');
                        $paths = [$remoteRsc, './' . $remoteRsc, 'disk/' . $remoteRsc, 'flash/' . $remoteRsc];
                        foreach ($paths as $p) {
                            if (@ftp_get($ftp, $tmp, $p, FTP_ASCII)) {
                                $content = @file_get_contents($tmp);
                                break 2;
                            }
                        }
                        if ($content !== null && $content !== false) break;
                        $list = @ftp_nlist($ftp, '.') ?: @ftp_nlist($ftp, '');
                        if (is_array($list)) {
                            foreach ($list as $f) {
                                $bn = basename($f);
                                if ($bn === $remoteRsc || stripos($bn, $fname) !== false) {
                                    if (@ftp_get($ftp, $tmp, $f, FTP_ASCII)) {
                                        $content = @file_get_contents($tmp);
                                        break 2;
                                    }
                                }
                            }
                        }
                        @unlink($tmp);
                    }
                    @ftp_delete($ftp, $remoteRsc);
                    ftp_close($ftp);
                }
            }
            if ($content !== null && $content !== false && strlen(trim($content)) > 0) {
                $lines = array_values(array_filter(array_map('trim', explode("\n", $content))));
            }
            // Hapus file dari router via API
            if (!empty($lines)) {
                $API4 = new \RouterosAPI();
                $API4->port = (int)$router->port;
                $API4->timeout = 5;
                if ($API4->connect($router->host, $router->username, $pass)) {
                    try {
                        @$API4->comm('/file/remove', ['=numbers' => $remoteRsc]);
                        @$API4->comm('/file/remove', ['=numbers' => $fname]);
                    } finally {
                        $API4->disconnect();
                    }
                }
            }
        }

        return implode("\n", $lines);
    }
}
