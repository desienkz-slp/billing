<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    public function __construct(
        private LicenseService $licenseService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $status = $this->licenseService->verify();

        // Active license — proceed normally
        if ($status->isActive()) {
            return $next($request);
        }

        // Grace period — add warning header + proceed
        if ($status->isGrace()) {
            $response = $next($request);

            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $data = $response->getData(true);
                $data['_license_warning'] = "Lisensi expired. Grace period tersisa {$status->daysRemaining()} hari.";
                $response->setData($data);
            }

            // For web responses, share warning with views
            view()->share('licenseWarning', "Lisensi akan berakhir dalam {$status->daysRemaining()} hari. Segera perpanjang.");

            return $response;
        }

        // Expired — read-only mode
        if ($status->isReadOnly()) {
            // Allow GET requests (read-only)
            if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
                view()->share('licenseExpired', true);
                view()->share('licenseWarning', 'Lisensi telah expired. Sistem dalam mode read-only.');
                return $next($request);
            }

            // Block write operations
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Lisensi telah expired. Sistem dalam mode read-only. Hubungi administrator untuk memperpanjang.',
                    'license' => $status->toArray(),
                ], 402);
            }

            return back()->with('error', 'Lisensi telah expired. Hubungi administrator untuk memperpanjang.');
        }

        return $next($request);
    }
}
