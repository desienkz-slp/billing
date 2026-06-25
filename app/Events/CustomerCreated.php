<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:20              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Events; use App\Models\Customer; use Illuminate\Broadcasting\InteractsWithSockets; use Illuminate\Foundation\Events\Dispatchable; use Illuminate\Queue\SerializesModels; class CustomerCreated { use Dispatchable, InteractsWithSockets, SerializesModels; public $customer; public $shouldSyncPppoe; public function __construct(Customer $k5kEn, bool $G8fMY = false) { $this->customer = $k5kEn; $this->shouldSyncPppoe = $G8fMY; } }