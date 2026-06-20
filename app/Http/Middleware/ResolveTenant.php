<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ResolveTenant - Mendeteksi tenant aktif dari request.
 * 
 * Strategi resolusi (urutan prioritas):
 * 1. Subdomain: blitar.ladapala.com → tenant "blitar"
 * 2. Header: X-Tenant-ID (untuk API calls)
 * 3. Session: tenant yang sudah di-lock setelah login
 * 4. Default: tenant pertama (development fallback)
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        // System admin accessing /superadmin routes → skip tenant resolution entirely
        if ($request->is('superadmin*') && $request->user()?->is_system_admin) {
            app()->instance('current_tenant', null);
            return $next($request);
        }

        $tenant = $this->resolveFromSubdomain($request)
            ?? $this->resolveFromHeader($request)
            ?? $this->resolveFromSession($request)
            ?? $this->resolveFromUser($request);

        if (!$tenant) {
            // Development/single-tenant fallback: gunakan tenant pertama
            $tenant = Tenant::first();
        }

        if ($tenant) {
            // System admin bypasses tenant active check
            if (!$tenant->is_active && !($request->user()?->is_system_admin)) {
                abort(403, 'Tenant tidak aktif. Hubungi administrator.');
            }

            // Bind tenant ke service container
            app()->instance('current_tenant', $tenant);

            // Set timezone sesuai tenant
            config(['app.timezone' => $tenant->timezone ?? 'Asia/Jakarta']);
        } else {
            // No tenant found — bind null (guest routes still work)
            app()->instance('current_tenant', null);
        }

        return $next($request);
    }

    /**
     * Resolve dari subdomain: blitar.ladapala.com
     */
    protected function resolveFromSubdomain(Request $request): ?Tenant
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // Minimal 3 parts: subdomain.domain.tld
        if (count($parts) >= 3) {
            $slug = $parts[0];
            // Skip common non-tenant subdomains
            if (!in_array($slug, ['www', 'api', 'admin', 'localhost'])) {
                return Tenant::where('slug', $slug)->first()
                    ?? Tenant::where('domain', $host)->first();
            }
        }

        // Custom domain check
        return Tenant::where('domain', $host)->first();
    }

    /**
     * Resolve dari header API: X-Tenant-ID
     */
    protected function resolveFromHeader(Request $request): ?Tenant
    {
        $tenantId = $request->header('X-Tenant-ID');
        if ($tenantId) {
            return Tenant::find($tenantId);
        }
        return null;
    }

    /**
     * Resolve dari session (setelah login)
     */
    protected function resolveFromSession(Request $request): ?Tenant
    {
        $tenantId = session('tenant_id');
        if ($tenantId) {
            return Tenant::find($tenantId);
        }
        return null;
    }

    /**
     * Resolve dari user yang sudah login
     */
    protected function resolveFromUser(Request $request): ?Tenant
    {
        $user = $request->user();
        if ($user && $user->tenant_id) {
            return Tenant::find($user->tenant_id);
        }
        return null;
    }
}
