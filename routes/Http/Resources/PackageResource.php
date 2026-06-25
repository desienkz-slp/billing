<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:51              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Resources; use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource; class PackageResource extends JsonResource { public function toArray(Request $u9qhv): array { return ['id' => $this->id, 'name' => $this->name, 'speed' => $this->speed, 'price' => $this->price, 'price_formatted' => 'Rp ' . number_format($this->price, 0, ',', '.'), 'description' => $this->description, 'is_active' => $this->is_active]; } }
