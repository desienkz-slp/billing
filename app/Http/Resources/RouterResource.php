<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:35              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Resources; use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource; class RouterResource extends JsonResource { public function toArray(Request $CaBx1): array { return ['id' => $this->id, 'name' => $this->name, 'ip_address' => $this->ip_address, 'port' => $this->port, 'description' => $this->description, 'is_active' => $this->is_active, 'customers_count' => $this->whenCounted('customers')]; } }
