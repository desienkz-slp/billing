<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Router;
use App\Services\MikroTik\MikroTikService;
use Illuminate\Http\Request;

class CustomerDiagnosticController extends Controller
{
    /**
     * Scan for duplicate PPPoE usernames.
     */
    public function scanDuplicates()
    {
        $customers = Customer::whereNotNull('username')
            ->where('username', '!=', '')
            ->whereNotNull('router_id')
            ->with('router')
            ->get();

        $groups = [];
        foreach ($customers as $c) {
            $key = $c->username . '|' . $c->router_id;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'username' => $c->username,
                    'router_id' => $c->router_id,
                    'router_name' => $c->router->name ?? 'Unknown',
                    'customers' => [],
                ];
            }
            $groups[$key]['customers'][] = [
                'id' => $c->id,
                'name' => $c->name,
            ];
        }

        $duplicates = [];
        foreach ($groups as $g) {
            if (count($g['customers']) >= 2) {
                $duplicates[] = $g;
            }
        }

        return response()->json([
            'status' => 'success',
            'duplicates' => array_values($duplicates),
            'total' => count($duplicates),
        ]);
    }

    /**
     * Scan for customers with empty PPPoE usernames.
     */
    public function scanUnsynced()
    {
        $customers = Customer::with('package')
            ->whereNull('username')
            ->orWhere('username', '')
            ->get();

        $unsynced = $customers->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'package' => $c->package->name ?? '-',
            ];
        });

        return response()->json([
            'status' => 'success',
            'unsynced' => $unsynced,
            'total' => $unsynced->count(),
        ]);
    }

    /**
     * Scan for mismatching profiles between billing and router.
     */
    public function scanMismatch(MikroTikService $mikrotikService)
    {
        $customers = Customer::whereNotNull('username')
            ->where('username', '!=', '')
            ->whereNotNull('router_id')
            ->with(['router', 'package'])
            ->get();

        $byRouter = $customers->groupBy('router_id');
        $mismatches = [];

        foreach ($byRouter as $routerId => $group) {
            $router = $group->first()->router;
            if (!$router || !$router->is_active) {
                continue;
            }

            try {
                $secrets = $mikrotikService->getPppoeSecrets($router);
                // Map secrets by name
                $routerProfiles = [];
                foreach ($secrets as $s) {
                    if (isset($s['name'])) {
                        $routerProfiles[$s['name']] = $s['profile'] ?? '';
                    }
                }

                foreach ($group as $c) {
                    if (isset($routerProfiles[$c->username])) {
                        $routerProfile = trim($routerProfiles[$c->username]);
                        
                        // Default expected profile is the package name.
                        // If you use PackageRouter relations, you'd fetch it here.
                        $expectedProfile = trim($c->package->name ?? '');

                        // Handle Isolir status
                        if ($c->is_isolated) {
                            $expectedProfile = 'ISOLIR'; // Change to your actual isolated profile name if different
                        }

                        if (strcasecmp($expectedProfile, $routerProfile) !== 0) {
                            $mismatches[] = [
                                'customer_id' => $c->id,
                                'nama' => $c->name,
                                'username' => $c->username,
                                'router_nama' => $router->name,
                                'paket_billing' => $c->package->name ?? '-',
                                'profile_router' => $routerProfile,
                                'expected_profile' => $expectedProfile,
                                'is_isolir' => $c->is_isolated,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignore router connection errors to not block the whole request,
                // or you could log them.
                \Log::warning("Failed to connect to router ID $routerId for mismatch scan: " . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'mismatches' => $mismatches,
            'total' => count($mismatches),
        ]);
    }
}
