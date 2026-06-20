@php
    $user = auth()->user()->load('role', 'tenant');
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
            'url' => '#', // To be updated when OLT module is ready
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
            'color' => '#ef4444',
        ];
    }

    // If only 1 module → redirect directly
    if (count($modules) === 1) {
        header('Location: ' . $modules[0]['url']);
        exit;
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Pilih Modul · {{ $tenant->name ?? 'Aplikasi' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',system-ui,-apple-system,sans-serif;background:#0b1120;color:#e2e8f0;min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden}
        .gateway-bg{position:fixed;inset:0;z-index:0;overflow:hidden}
        .gateway-orb{position:absolute;border-radius:50%;filter:blur(80px);opacity:.25;animation:float 8s ease-in-out infinite}
        .gateway-orb-1{width:400px;height:400px;background:radial-gradient(circle,#0ea5e9,transparent);top:-100px;right:-100px;animation-delay:0s}
        .gateway-orb-2{width:350px;height:350px;background:radial-gradient(circle,#8b5cf6,transparent);bottom:-80px;left:-80px;animation-delay:2s}
        .gateway-orb-3{width:250px;height:250px;background:radial-gradient(circle,#10b981,transparent);top:50%;left:50%;transform:translate(-50%,-50%);animation-delay:4s}
        @keyframes float{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(-20px) scale(1.05)}}
        .gateway-card{position:relative;z-index:1;width:100%;max-width:480px;margin:1.5rem;background:rgba(15,23,42,.85);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);border-radius:20px;padding:2.5rem;box-shadow:0 25px 50px rgba(0,0,0,.5)}
        .gateway-header{text-align:center;margin-bottom:2rem}
        .gateway-logo{width:56px;height:56px;background:linear-gradient(135deg,#0ea5e9,#6366f1);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:24px;font-weight:800;color:#fff;box-shadow:0 8px 25px rgba(14,165,233,.3)}
        .gateway-title{font-size:1.5rem;font-weight:700;color:#fff;margin-bottom:.5rem}
        .gateway-subtitle{font-size:.9rem;color:#94a3b8}
        .app-choice{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin:1.5rem 0}
        .app-choice a{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem 1.5rem;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:16px;text-decoration:none;color:#e2e8f0;transition:all .3s cubic-bezier(.4,0,.2,1);min-height:150px;position:relative;overflow:hidden}
        .app-choice a::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at center,var(--module-color,.1),transparent 70%);opacity:0;transition:opacity .3s}
        .app-choice a:hover{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.2);transform:translateY(-4px);box-shadow:0 12px 30px rgba(0,0,0,.3)}
        .app-choice a:hover::before{opacity:.15}
        .app-choice a svg{margin-bottom:.75rem;transition:transform .3s}
        .app-choice a:hover svg{transform:scale(1.1)}
        .app-choice a .module-label{font-weight:600;font-size:1rem;margin-bottom:.25rem}
        .app-choice a .module-desc{font-size:.75rem;color:#94a3b8;text-align:center}
        .gateway-footer{margin-top:1.5rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,.06);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem}
        .user-pill{font-size:.8rem;color:#94a3b8;display:flex;align-items:center;gap:.5rem}
        .user-pill .avatar{width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff}
        .logout-btn{color:#64748b;text-decoration:none;font-size:.85rem;display:flex;align-items:center;gap:.35rem;transition:color .2s}
        .logout-btn:hover{color:#f87171}
    </style>
</head>
<body>
    <div class="gateway-bg">
        <div class="gateway-orb gateway-orb-1"></div>
        <div class="gateway-orb gateway-orb-2"></div>
        <div class="gateway-orb gateway-orb-3"></div>
    </div>
    <div class="gateway-card">
        <div class="gateway-header">
            <div class="gateway-logo">{{ strtoupper(substr($tenant->name ?? 'A', 0, 1)) }}</div>
            <h1 class="gateway-title">Pilih Modul</h1>
            <p class="gateway-subtitle">Mau masuk ke aplikasi mana?</p>
        </div>
        <div class="app-choice">
            @foreach($modules as $m)
            <a href="{{ $m['url'] }}" style="--module-color:{{ $m['color'] }}">
                <span style="color:{{ $m['color'] }}">{!! $m['icon'] !!}</span>
                <span class="module-label">{{ $m['label'] }}</span>
                <span class="module-desc">{{ $m['description'] }}</span>
            </a>
            @endforeach
        </div>
        <div class="gateway-footer">
            <span class="user-pill">
                <span class="avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
                {{ $user->name }} ({{ $user->role->name ?? 'User' }})
            </span>
            <form method="POST" action="{{ route('logout', [], false) }}" style="display:inline">
                @csrf
                <button type="submit" class="logout-btn" style="background:none;border:none;cursor:pointer;font-family:inherit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
    <div style="position:absolute;bottom:20px;width:100%;text-align:center;color:#64748b;font-size:0.75rem;">
        &copy; 2026 upluk-upluk_dev version 2.0
    </div>
</body>
</html>
