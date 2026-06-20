<?php
use App\Models\Role;

$roles = Role::whereIn('name', ['Sales', 'Agent'])->get();
foreach ($roles as $role) {
    $role->can_process_payment = true;
    $role->save();
}
echo "Role can_process_payment updated.\n";
