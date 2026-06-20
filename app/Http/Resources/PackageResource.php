<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'speed' => $this->speed,
            'price' => $this->price,
            'price_formatted' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];
    }
}
