<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SuperAdminController extends Controller
{
    /** Redirect to tenants page */
    public function index()
    {
        return redirect()->route('superadmin.tenants');
    }

    // ==================== TENANTS ====================

    public function tenants()
    {
        $tenants = Tenant::where('slug', '!=', 'system')
            ->withCount(['users', 'customers'])
            ->with(['subscriptions' => function ($q) {
                $q->orderByDesc('expires_at')->limit(1);
            }])
            ->orderBy('name')
            ->get();

        return Inertia::render('SuperAdmin/Tenants', compact('tenants'));
    }

    public function storeTenant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:tenants,slug',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
            'timezone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:20',
            'admin_username' => 'required|string|max:100',
            'admin_password' => 'required|string|min:6',
            'admin_name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create tenant
            $tenant = Tenant::create([
                'uuid' => (string) Str::uuid(),
                'name' => $request->name,
                'slug' => $request->slug ?: Str::slug($request->name),
                'domain' => $request->domain,
                'timezone' => $request->timezone ?: 'Asia/Jakarta',
                'company_profile' => [
                    'name' => $request->company_name ?: $request->name,
                    'address' => $request->company_address,
                    'phone' => $request->company_phone,
                ],
                'billing_config' => [
                    'ppn_rate' => 11,
                    'auto_isolir_days' => 20,
                    'billing_cycle_day' => 1,
                    'invoice_prefix' => 'INV',
                    'receipt_prefix' => 'RCP',
                    'currency' => 'IDR',
                ],
                'is_active' => true,
            ]);

            // 2. Create default Superadmin role
            $role = Role::withoutGlobalScopes()->create([
                'tenant_id' => $tenant->id,
                'name' => 'Superadmin',
                'description' => 'Akses penuh ke semua fitur',
                'can_view_dashboard' => true, 'can_view_dashboard_config' => true, 'can_view_dashboard_map' => true, 'can_view_dashboard_olt' => true, 'can_view_monitor' => true, 'can_view_radius' => true, 'can_view_acs' => true,
                'can_input_customer' => true, 'can_edit_customer' => true, 'can_delete_customer' => true, 'can_delete_customer_cuti' => true,
                'can_view_all_customers' => true, 'can_import_export_customer' => true,
                'can_process_payment' => true, 'can_send_wa_invoice' => true, 'can_cancel_payment' => true, 'can_view_payment_history' => true,
                'can_manage_isolir' => true, 'can_cuti' => true, 'can_manage_cuti' => true,
                'can_view_reports' => true, 'can_view_finance' => true, 'can_manage_expenses' => true,
                'can_manage_deposits' => true, 'can_manage_saldo' => true, 'can_delete_finance' => true,
                'can_manage_packages' => true, 'can_manage_areas' => true, 'can_manage_odp' => true,
                'can_manage_router' => true, 'can_manage_olt' => true, 'can_manage_acs' => true,
                'can_manage_users' => true, 'can_manage_roles' => true, 'can_view_audit_logs' => true,
                'can_backup_restore' => true, 'can_edit_template' => true,
                'can_manage_radius' => true, 'can_send_wa_blast' => true, 'can_config_map' => true,
            ]);

            // 3. Create admin user
            User::withoutGlobalScopes()->create([
                'uuid' => (string) Str::uuid(),
                'tenant_id' => $tenant->id,
                'role_id' => $role->id,
                'username' => $request->admin_username,
                'name' => $request->admin_name,
                'password' => $request->admin_password,
                'is_active' => true,
            ]);

            // 4. Create default subscription (trial 30 days)
            TenantSubscription::create([
                'tenant_id' => $tenant->id,
                'plan' => 'trial',
                'license_key' => 'LIC-' . strtoupper(Str::random(16)),
                'max_customers' => 50,
                'features' => ['all'],
                'starts_at' => now(),
                'expires_at' => now()->addDays(30),
                'status' => 'active',
            ]);

            DB::commit();
            return back()->with('success', "Tenant \"{$tenant->name}\" berhasil dibuat dengan user admin \"{$request->admin_username}\".");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', "Gagal membuat tenant: {$e->getMessage()}")->withInput();
        }
    }

    public function updateTenant(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:tenants,slug,' . $tenant->id,
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $tenant->id,
            'timezone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:20',
        ]);

        $tenant->update([
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'domain' => $request->domain,
            'timezone' => $request->timezone ?: 'Asia/Jakarta',
            'company_profile' => [
                'name' => $request->company_name ?: $request->name,
                'address' => $request->company_address,
                'phone' => $request->company_phone,
            ],
        ]);

        return response()->json(['status' => 'success', 'message' => 'Tenant berhasil diperbarui.']);
    }

    public function toggleTenant(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);
        $status = $tenant->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json(['status' => 'success', 'message' => "Tenant \"{$tenant->name}\" berhasil {$status}."]);
    }

    public function destroyTenant(Request $request, Tenant $tenant)
    {
        // Protect System tenant (houses system admin user)
        if ($tenant->slug === 'system') {
            return response()->json(['status' => 'error', 'message' => 'Tenant "System" tidak bisa dihapus.'], 400);
        }

        DB::beginTransaction();
        try {
            // Delete all tenant-related data (but PROTECT system admin users)
            User::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->where('is_system_admin', false)
                ->delete();
            Role::withoutGlobalScopes()->where('tenant_id', $tenant->id)->delete();
            TenantSubscription::where('tenant_id', $tenant->id)->delete();

            // Delete tenant-aware models if they exist
            $tenantModels = [
                Customer::class, \App\Models\Package::class, \App\Models\Area::class,
                \App\Models\Router::class, \App\Models\Invoice::class, \App\Models\Payment::class,
            ];
            foreach ($tenantModels as $model) {
                if (class_exists($model)) {
                    $model::withoutGlobalScopes()->where('tenant_id', $tenant->id)->delete();
                }
            }

            $tenant->delete();
            DB::commit();

            return response()->json(['status' => 'success', 'message' => "Tenant \"{$tenant->name}\" dan semua datanya berhasil dihapus."]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => "Gagal menghapus: {$e->getMessage()}"], 500);
        }
    }

    // ==================== SUBSCRIPTIONS ====================

    public function subscriptions()
    {
        $subscriptions = TenantSubscription::with('tenant')
            ->orderByDesc('created_at')
            ->get();

        $tenants = Tenant::orderBy('name')->get(['id', 'name']);

        return inertia('SuperAdmin/Subscriptions', compact('subscriptions', 'tenants'));
    }

    public function saasPackages()
    {
        // For UI purposes, we just return the view.
        // In a real implementation, you would pass data from a saas_packages table.
        return inertia('SuperAdmin/Packages');
    }

    public function storeSubscription(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'plan' => 'required|string|max:50',
            'max_customers' => 'required|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'status' => 'required|in:active,expired,grace,cancelled',
        ]);

        TenantSubscription::create([
            'tenant_id' => $request->tenant_id,
            'plan' => $request->plan,
            'license_key' => 'LIC-' . strtoupper(Str::random(16)),
            'max_customers' => $request->max_customers,
            'features' => ['all'],
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Subscription berhasil ditambahkan.');
    }

    public function updateSubscription(Request $request, TenantSubscription $subscription)
    {
        $request->validate([
            'plan' => 'required|string|max:50',
            'max_customers' => 'required|integer|min:1',
            'expires_at' => 'required|date',
            'status' => 'required|in:active,expired,grace,cancelled',
        ]);

        $subscription->update([
            'plan' => $request->plan,
            'max_customers' => $request->max_customers,
            'expires_at' => $request->expires_at,
            'status' => $request->status,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Subscription berhasil diperbarui.']);
    }

    // ==================== TENANT DETAIL ====================

    public function tenantDetail(Tenant $tenant)
    {
        $tenant->loadCount(['users', 'customers']);

        $users = User::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->with('role')
            ->orderBy('name')
            ->get();

        $roles = Role::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->withCount('users')
            ->orderBy('name')
            ->get();

        $subscriptions = TenantSubscription::where('tenant_id', $tenant->id)
            ->orderByDesc('created_at')
            ->get();

        // Stats
        $stats = [
            'total_users' => $users->count(),
            'active_users' => $users->where('is_active', true)->count(),
            'total_customers' => $tenant->customers_count,
            'total_roles' => $roles->count(),
        ];

        return inertia('SuperAdmin/TenantDetail', compact('tenant', 'users', 'roles', 'subscriptions', 'stats'));
    }

    // ==================== PROFILE ====================

    public function profile()
    {
        $user = auth()->user();
        return inertia('SuperAdmin/Profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'email' => 'nullable|email|max:255',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        // Change password if provided
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Password lama tidak sesuai.')->withInput();
            }
            $data['password'] = $request->new_password;
        }

        User::withoutGlobalScopes()->where('id', $user->id)->update(
            collect($data)->when(isset($data['password']), function ($col) {
                return $col->merge(['password' => Hash::make($col['password'])]);
            })->toArray()
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
