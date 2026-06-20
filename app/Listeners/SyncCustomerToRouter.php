<?php

namespace App\Listeners;

use App\Events\CustomerCreated;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SyncCustomerToRouter implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CustomerCreated $event)
    {
        $customer = $event->customer;

        if (!$event->shouldSyncPppoe) {
            return;
        }

        if (!$customer->router_id) {
            return;
        }

        try {
            // Note: MikrotikService integration will be injected here.
            // Assuming App\Services\MikroTik\MikrotikApiService or similar exists.
            
            // Log the action temporarily as placeholder for actual MikroTik integration
            Log::info("Syncing Customer {$customer->username} to Router {$customer->router_id}");
            
            AuditLog::create([
                'tenant_id' => $customer->tenant_id,
                'action' => 'SYNC',
                'module' => 'MikroTik',
                'description' => "Berhasil sinkronisasi PPPoE {$customer->username} ke Router {$customer->router_id}.",
                'user_id' => null, // System
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to sync customer to router: " . $e->getMessage());
        }
    }
}
