<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private PermissionService $permissionService
    ) {}

    /**
     * Login via API (returns Sanctum token)
     *
     * POST /api/v1/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::with('role', 'tenant')
            ->where('username', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'username' => ['Akun Anda telah dinonaktifkan. Hubungi administrator.'],
            ]);
        }

        if (!$user->tenant || !$user->tenant->is_active) {
            throw ValidationException::withMessages([
                'username' => ['Tenant tidak aktif. Hubungi administrator.'],
            ]);
        }

        // Revoke existing tokens (single device login)
        $user->tokens()->delete();

        // Create new token with abilities based on role
        $abilities = $this->permissionService->getUserCapabilities($user);
        $token = $user->createToken(
            name: $request->device_name ?? 'api-token',
            abilities: $abilities,
            expiresAt: now()->addDays(30)
        );

        // Update last login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'username' => $user->username,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role?->name,
                    'tenant' => [
                        'id' => $user->tenant->id,
                        'name' => $user->tenant->name,
                        'slug' => $user->tenant->slug,
                    ],
                    'permissions' => $abilities,
                ],
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => now()->addDays(30)->toIso8601String(),
            ],
        ]);
    }

    /**
     * Logout (revoke current token)
     *
     * POST /api/v1/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * Get current user info
     *
     * GET /api/v1/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('role', 'tenant');
        $abilities = $this->permissionService->getUserCapabilities($user);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->name,
                'tenant' => [
                    'id' => $user->tenant->id,
                    'name' => $user->tenant->name,
                    'slug' => $user->tenant->slug,
                ],
                'permissions' => $abilities,
                'last_login_at' => $user->last_login_at,
            ],
        ]);
    }
}
