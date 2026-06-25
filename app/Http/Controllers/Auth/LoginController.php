<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    /**
     * Show login page
     */
    public function showLoginForm(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle login form submission
     */
    public function login(LoginRequest $request): \Symfony\Component\HttpFoundation\Response
    {
        $user = User::withoutGlobalScopes()
            ->with('role', 'tenant')
            ->where('username', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'username' => ['Akun Anda telah dinonaktifkan.'],
            ]);
        }

        // Tenant check — skip for system admin (they manage tenants, don't need one)
        if (!$user->is_system_admin && (!$user->tenant || !$user->tenant->is_active)) {
            throw ValidationException::withMessages([
                'username' => ['Tenant tidak aktif.'],
            ]);
        }

        // Platform access check — only for web login (desktop)
        if (!$user->is_system_admin && $user->role && !$user->role->can_access_desktop) {
            throw ValidationException::withMessages([
                'username' => ['Role Anda tidak memiliki izin untuk login melalui browser web.'],
            ]);
        }

        // Login via session
        Auth::login($user, $request->boolean('remember'));

        // Update last login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $request->session()->regenerate();

        // Store tenant in session (AFTER regenerate so it persists)
        session(['tenant_id' => $user->tenant_id]);

        return Inertia::location(route('app-gateway', [], false));
    }

    /**
     * Logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login', [], false));
    }
}
