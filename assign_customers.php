<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Package;

$tenantId = 1;

// 1. Get default sales user or user ID 1
$defaultUser = User::where('is_default_sales', true)->first() ?? User::find(1);

if ($defaultUser) {
    // Assign all existing customers to defaultUser
    Customer::whereNull('sales_id')->orWhere('sales_id', 0)->orWhereNotNull('id')->update(['sales_id' => $defaultUser->id]);
    echo "Assigned all existing customers to {$defaultUser->name}.\n";
}

// 2. Create fake customers for the new users
$newUsers = User::whereIn('username', ['budisales', 'sitisales', 'agusagent', 'rinaagent'])->get();
$package = Package::first();

if (!$package) {
    echo "No package available. Please create a package first.\n";
} else {
    foreach ($newUsers as $u) {
        $numCustomers = rand(3, 8);
        for ($i = 1; $i <= $numCustomers; $i++) {
            Customer::create([
                'tenant_id' => $tenantId,
                'name' => "Pelanggan " . explode(' ', $u->name)[0] . " " . $i,
                'phone' => '08' . rand(1000000000, 9999999999),
                'address' => 'Alamat otomatis ' . $i,
                'sales_id' => $u->id,
                'package_id' => $package->id,
                'status' => 'active',
                'auto_isolir' => true,
                'max_tunggakan' => 0,
                'registration_date' => now(),
            ]);
        }
        echo "Created {$numCustomers} customers for {$u->name}.\n";
    }
}
