<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:
     *   ->middleware('permission:can_process_payment')
     *   ->middleware('permission:can_manage_users,can_manage_roles')  // ANY of these
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Akses ditolak.');
        }

        // System admin bypasses all permission checks
        if ($user->is_system_admin) {
            return $next($request);
        }

        $role = $user->role;

        if (!$role) {
            abort(403, 'Role tidak ditemukan. Hubungi administrator.');
        }

        // Check if role has ANY of the required permissions
        foreach ($permissions as $permission) {
            $perm = trim($permission);
            if ($role->hasPermission($perm)) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
