<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GatewayController extends Controller
{
    public function index(Request $request): Response|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user()->load('role', 'tenant');
        $role = $user->role;
        $tenant = $user->tenant;
        $isSystemAdmin = $user->is_system_admin;

        // Determine accessible modules based on role permissions
        $modules = [];

        // 1. Billing
        if ($isSystemAdmin || $role?->can_view_dashboard) {
            $modules[] = [
                'key' => 'billing',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>',
                'label' => 'Billing',
                'description' => 'Pelanggan, pembayaran, laporan',
                'url' => route('cust.dashboard', [], false),
                'image_url' => asset('images/modules/billing.png?v=4'),
                'color' => '#0ea5e9',
            ];
        }

        // 2. Map
        if ($isSystemAdmin || $role?->can_view_dashboard_map) {
            $modules[] = [
                'key' => 'map',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
                'label' => 'Peta',
                'description' => 'Peta pelanggan & ODP',
                'url' => route('map.index', [], false),
                'image_url' => asset('images/modules/peta.png?v=4'),
                'color' => '#10b981',
            ];
        }

        // 3. ROS (formerly Monitoring)
        if ($isSystemAdmin || $role?->can_view_monitor) {
            $modules[] = [
                'key' => 'monitoring',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
                'label' => 'ROS',
                'description' => 'Status & monitoring router',
                'url' => route('monitoring.index', [], false),
                'image_url' => asset('images/modules/ros.png?v=4'),
                'color' => '#22c55e',
            ];
        }

        // 4. RADIUS
        if ($isSystemAdmin || $role?->can_view_radius) {
            $modules[] = [
                'key' => 'radius',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/></svg>',
                'label' => 'RADIUS',
                'description' => 'PPPoE & RADIUS management',
                'url' => route('radius.index', [], false),
                'image_url' => asset('images/modules/radius.png?v=4'),
                'color' => '#8b5cf6',
            ];
        }

        // 5. OLT
        if ($isSystemAdmin || $role?->can_view_dashboard_olt) {
            $modules[] = [
                'key' => 'olt',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="6" y1="14" x2="6.01" y2="14"/><line x1="10" y1="14" x2="10.01" y2="14"/></svg>',
                'label' => 'OLT',
                'description' => 'Integrasi perangkat OLT',
                'url' => route('olt.index', [], false),
                'image_url' => asset('images/modules/olt.png?v=4'),
                'color' => '#f97316', // Orange
            ];
        }

        // 6. ACS
        if ($isSystemAdmin || $role?->can_view_acs) {
            $modules[] = [
                'key' => 'acs',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>',
                'label' => 'ACS',
                'description' => 'Manajemen ONT / Router TR-069',
                'url' => route('acs.index', [], false),
                'image_url' => asset('images/modules/acs.png?v=4'),
                'color' => '#ec4899', // Pink
            ];
        }

        // 7. Config
        if ($isSystemAdmin || $role?->can_view_dashboard_config) {
            $modules[] = [
                'key' => 'config',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
                'label' => 'Config',
                'description' => 'User, role, router, nota',
                'url' => route('config.index', [], false),
                'image_url' => asset('images/modules/config.png?v=4'),
                'color' => '#f59e0b',
            ];
        }

        // 8. Super Admin (Tenant) — only for system admin
        if ($isSystemAdmin) {
            $modules[] = [
                'key' => 'superadmin',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
                'label' => 'Tenant',
                'description' => 'Kelola tenant & subscription',
                'url' => route('superadmin.index', [], false),
                'image_url' => asset('images/modules/tenant.png?v=4'),
                'color' => '#ef4444',
            ];
        }

        // If only 1 module -> redirect directly
        if (count($modules) === 1) {
            return redirect($modules[0]['url']);
        }

        return Inertia::render('AppGateway', [
            'modules' => $modules,
            'user' => [
                'name' => $user->name,
                'role_name' => $role?->name ?? 'User',
            ],
            'tenant' => [
                'name' => $tenant?->name ?? 'Aplikasi',
            ],
            'city_day_url' => asset('images/city_day.png'),
            'city_night_url' => asset('images/city_night.png'),
        ]);
    }
}
