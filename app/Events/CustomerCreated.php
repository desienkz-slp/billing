<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:50              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Events; use App\Models\Customer; use Illuminate\Broadcasting\InteractsWithSockets; use Illuminate\Foundation\Events\Dispatchable; use Illuminate\Queue\SerializesModels; class CustomerCreated { use Dispatchable, InteractsWithSockets, SerializesModels; public $customer; public $shouldSyncPppoe; public function __construct(Customer $ygIPj, bool $lx5fi = false) { $this->customer = $ygIPj; $this->shouldSyncPppoe = $lx5fi; } }
