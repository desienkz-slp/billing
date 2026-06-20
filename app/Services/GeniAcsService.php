<?php

namespace App\Services;

use App\Models\Server;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeniAcsService
{
    public function rebootDevice(string $pppoeUsername): bool
    {
        $server = Server::where('type', 'geniacs')->first();
        if (!$server) {
            return false;
        }

        $host = rtrim($server->host, '/');
        $port = $server->port ?? 7557; // Default NBI port
        $url = "http://{$host}:{$port}/devices";

        // Query device by VirtualParameters.pppoe_username OR InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.Username
        // As user suggested, check wan ppp user or virtual parameter
        $query = '{"$or": [{"VirtualParameters.pppoe_username": "'.$pppoeUsername.'"}, {"InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.Username": "'.$pppoeUsername.'"}]}';

        try {
            $response = Http::withBasicAuth($server->username, $server->getDecryptedPassword())
                ->get($url, ['query' => $query]);

            if ($response->successful()) {
                $devices = $response->json();
                if (empty($devices)) {
                    return false; // Not found
                }

                $deviceId = $devices[0]['_id'];

                // Send reboot task
                $taskUrl = "http://{$host}:{$port}/devices/" . urlencode($deviceId) . "/tasks";
                $taskResponse = Http::withBasicAuth($server->username, $server->getDecryptedPassword())
                    ->post($taskUrl, [
                        'name' => 'reboot'
                    ]);

                return $taskResponse->successful();
            }
        } catch (\Throwable $e) {
            Log::error("GeniACS Reboot Failed: " . $e->getMessage());
        }

        return false;
    }
}
