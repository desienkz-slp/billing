<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SystemAdmin - Memastikan hanya user dengan is_system_admin = true
 * yang bisa mengakses route /superadmin/*.
 */
class SystemAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->is_system_admin) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Hanya System Admin.'], 403);
            }
            abort(403, 'Akses ditolak. Hanya System Admin.');
        }

        return $next($request);
    }
}
