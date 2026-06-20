<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use App\Models\Package;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

$tenantId = 1;

// 1. Create or get Role with is_saldo_limited = true
$role = Role::firstOrCreate(
    ['name' => 'Reseller Saldo', 'tenant_id' => $tenantId],
    [
        'can_process_payment' => true,
        'can_view_customers' => true,
        'can_view_dashboard' => true,
        'can_input_customer' => true,
        'is_saldo_limited' => true, // Pembatasan saldo aktif
    ]
);

// Pastikan jika sudah ada, diupdate is_saldo_limited nya
$role->update([
    'is_saldo_limited' => true,
    'can_process_payment' => true,
]);

echo "Role 'Reseller Saldo' created/updated.\n";

// 2. Create User
$username = 'budi_reseller';
$user = User::firstOrCreate(
    ['username' => $username, 'tenant_id' => $tenantId],
    [
        'name' => 'Budi Reseller',
        'email' => 'budi_reseller@example.com',
        'password' => Hash::make('password123'),
        'role_id' => $role->id,
        'is_active' => true,
        'deposit_balance' => 0, // Saldo awal 0
        'phone' => '0899998888',
        'is_system_admin' => false,
        'is_default_sales' => false,
        'uuid' => (string) Str::uuid(),
    ]
);

// Pastikan role diset benar jika user sudah ada
if ($user->role_id !== $role->id) {
    $user->update(['role_id' => $role->id]);
}

echo "User '{$user->name}' created.\n";

// 3. Create Customers
$package = Package::first();
if (!$package) {
    echo "No package found, cannot create customers.\n";
} else {
    // Buat 3 pelanggan
    $count = 0;
    for ($i = 1; $i <= 3; $i++) {
        Customer::firstOrCreate(
            [
                'phone' => '08777777770' . $i, 
                'tenant_id' => $tenantId
            ],
            [
                'name' => 'Pelanggan Reseller ' . $i,
                'address' => 'Jl. Reseller No ' . $i,
                'sales_id' => $user->id,
                'package_id' => $package->id,
                'status' => 'active',
                'auto_isolir' => true,
                'max_tunggakan' => 0,
                'registration_date' => now(),
            ]
        );
        $count++;
    }
    echo "Created {$count} customers for {$user->name}.\n";
}
