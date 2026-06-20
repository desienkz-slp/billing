<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'nik' => 'nullable|string|max:20',
            'package_id' => 'required|exists:packages,id',
            'area_id' => 'required|exists:areas,id',
            'router_id' => 'nullable|exists:routers,id',
            'server_id' => 'nullable|exists:servers,id',
            'odp_id' => 'nullable|exists:odps,id',
            'sales_id' => 'nullable|exists:users,id',
            'registration_date' => 'nullable|date',
            'billing_date' => 'nullable|integer|min:1|max:28',
            'custom_price' => 'nullable|integer|min:0',
            'password_pppoe' => 'nullable|string|max:100',
            'odp_port' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'package_id.required' => 'Paket wajib dipilih.',
            'area_id.required' => 'Area wajib dipilih.',
        ];
    }
}
