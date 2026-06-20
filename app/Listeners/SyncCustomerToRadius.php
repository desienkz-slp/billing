<?php

namespace App\Listeners;

use App\Events\CustomerCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SyncCustomerToRadius implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CustomerCreated $event)
    {
        $customer = $event->customer;

        if (!$event->shouldSyncPppoe) {
            return;
        }

        if (!$customer->server_id) { // server_id maps to RADIUS server in Laravel
            return;
        }

        try {
            // Note: RadiusService integration will be injected here.
            // Assuming App\Services\RadiusService exists and works with FreeRADIUS DB
            Log::info("Syncing Customer {$customer->username} to RADIUS DB (radcheck/radusergroup)");

            // Example integration placeholder
            // $radiusService = app(RadiusService::class);
            // $radiusService->createRadiusUser($customer->username, $customer->password_pppoe, 'default');

        } catch (\Exception $e) {
            Log::error("Failed to sync customer to RADIUS: " . $e->getMessage());
        }
    }
}
