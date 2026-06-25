<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use App\Models\AuditLog; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SendCustomerCreationNotification implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $zUxeQ) { $ygIPj = $zUxeQ->customer; try { AuditLog::create(['tenant_id' => $ygIPj->tenant_id, 'action' => 'CREATE', 'module' => 'Customer', 'description' => "Menambahkan pelanggan baru: {$ygIPj->name} (ID: {$ygIPj->id})", 'user_id' => auth()->id() ?? null]); Log::info("FCM Notification sent for new customer {$ygIPj->name}"); } catch (\Exception $a_tGM) { Log::error("Failed to send customer notification: " . $a_tGM->getMessage()); } } }
