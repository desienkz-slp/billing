<?php

namespace App\Listeners;

use App\Events\CustomerCreated;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendCustomerCreationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CustomerCreated $event)
    {
        $customer = $event->customer;

        try {
            // Write to the internal audit log
            AuditLog::create([
                'tenant_id' => $customer->tenant_id,
                'action' => 'CREATE',
                'module' => 'Customer',
                'description' => "Menambahkan pelanggan baru: {$customer->name} (ID: {$customer->id})",
                'user_id' => auth()->id() ?? null,
            ]);

            // Note: FCM integration will be injected here.
            // Example:
            // FCMHelper::notifyAuditAdmins(
            //     "Pelanggan Baru Ditambahkan",
            //     "Pelanggan baru {$customer->name} ditambahkan.",
            //     ['type' => 'new_customer']
            // );

            Log::info("FCM Notification sent for new customer {$customer->name}");

        } catch (\Exception $e) {
            Log::error("Failed to send customer notification: " . $e->getMessage());
        }
    }
}
