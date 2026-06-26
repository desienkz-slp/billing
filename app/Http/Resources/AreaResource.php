<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:32              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Resources; use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource; class AreaResource extends JsonResource { public function toArray(Request $DVgdY): array { return ['id' => $this->id, 'name' => $this->name, 'description' => $this->description, 'customers_count' => $this->whenCounted('customers')]; } }
