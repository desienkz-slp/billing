<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'port' => $this->port,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'customers_count' => $this->whenCounted('customers'),
        ];
    }
}
