<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Server;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RadiusApiService
{
    /**
     * Push customer credentials to all active RADIUS API endpoints.
     */
    public function pushCustomer(Customer $customer): void
    {
        if (!$customer->sync_db_pusat) {
            return;
        }

        // Get all active API servers
        $servers = Server::radiusApi()->where('is_active', true)->get();

        if ($servers->isEmpty()) {
            return;
        }

        $payload = [
            'username' => $customer->username,
            'password' => $customer->getDecryptedPassword(),
            'profile'  => $customer->package->name ?? 'default',
            'action'   => 'save'
        ];

        foreach ($servers as $server) {
            $this->sendRequest($server, $payload);
        }
    }

    /**
     * Send delete request to all active RADIUS API endpoints.
     */
    public function deleteCustomer(Customer $customer): void
    {
        if (!$customer->sync_db_pusat) {
            return;
        }

        $servers = Server::radiusApi()->where('is_active', true)->get();

        if ($servers->isEmpty()) {
            return;
        }

        $payload = [
            'username' => $customer->username,
            'action'   => 'delete'
        ];

        foreach ($servers as $server) {
            $this->sendRequest($server, $payload);
        }
    }

    private function sendRequest(Server $server, array $payload): void
    {
        try {
            $endpoint = rtrim($server->api_endpoint, '/');
            $token = $server->getDecryptedApiToken();

            $response = Http::withToken($token)
                ->timeout(10)
                ->post($endpoint, $payload);

            if ($response->failed()) {
                Log::error("RadiusApiService: Failed to push to {$server->name} ({$endpoint})", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload
                ]);
            }
        } catch (\Exception $e) {
            Log::error("RadiusApiService: Exception when pushing to {$server->name}", [
                'error' => $e->getMessage()
            ]);
        }
    }
}
