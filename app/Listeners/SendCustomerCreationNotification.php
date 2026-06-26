<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:32              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use App\Models\AuditLog; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SendCustomerCreationNotification implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $A3CEf) { $RX5oA = $A3CEf->customer; try { AuditLog::create(['tenant_id' => $RX5oA->tenant_id, 'action' => 'CREATE', 'module' => 'Customer', 'description' => "Menambahkan pelanggan baru: {$RX5oA->name} (ID: {$RX5oA->id})", 'user_id' => auth()->id() ?? null]); Log::info("FCM Notification sent for new customer {$RX5oA->name}"); } catch (\Exception $UQk5V) { Log::error("Failed to send customer notification: " . $UQk5V->getMessage()); } } }
