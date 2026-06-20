<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

$tenantId = 1;

$salesRole = Role::where('name', 'Sales')->first();
$agentRole = Role::firstOrCreate(['name' => 'Agent'], [
    'tenant_id' => $tenantId,
    'permissions' => ['billing.dashboard.view', 'billing.customers.view', 'billing.payments.create'] // basic permissions
]);

$users = [
    [
        'name' => 'Budi Sales',
        'username' => 'budisales',
        'email' => 'budi@example.com',
        'role_id' => $salesRole->id ?? null,
        'fee_persen' => 5,
        'fee_fix' => 1000,
    ],
    [
        'name' => 'Siti Sales',
        'username' => 'sitisales',
        'email' => 'siti@example.com',
        'role_id' => $salesRole->id ?? null,
        'fee_persen' => 10,
        'fee_fix' => 0,
    ],
    [
        'name' => 'Agus Agent',
        'username' => 'agusagent',
        'email' => 'agus@example.com',
        'role_id' => $agentRole->id ?? null,
        'fee_persen' => 0,
        'fee_fix' => 5000,
    ],
    [
        'name' => 'Rina Agent',
        'username' => 'rinaagent',
        'email' => 'rina@example.com',
        'role_id' => $agentRole->id ?? null,
        'fee_persen' => 2,
        'fee_fix' => 2000,
    ]
];

foreach ($users as $u) {
    User::firstOrCreate(
        ['username' => $u['username']],
        [
            'tenant_id' => $tenantId,
            'name' => $u['name'],
            'email' => $u['email'],
            'password' => Hash::make('password123'),
            'role_id' => $u['role_id'],
            'is_active' => true,
            'fee_persen' => $u['fee_persen'],
            'fee_fix' => $u['fee_fix'],
            'uuid' => (string) Str::uuid()
        ]
    );
}

echo "Users created successfully.\n";
