<?php
use App\Models\User;

$users = User::with('role')->withCount('customers')->get();

$ps = app(\App\Services\PermissionService::class);

$output = [];
foreach ($users as $u) {
    $canBayar = $ps->userCan($u, 'billing.payments.create');
    $output[] = [
        'id' => $u->id,
        'name' => $u->name,
        'role' => $u->role->name ?? null,
        'can_bayar' => $canBayar,
        'customers_count' => $u->customers_count,
        'permissions' => $u->role->permissions ?? null,
    ];
}

echo json_encode($output, JSON_PRETTY_PRINT);
