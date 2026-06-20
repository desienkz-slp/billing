<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    public function __construct(private PermissionService $permissionService) {}

    public function index(Request $request)
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        $query = Customer::with(['package', 'area', 'sales'])
            ->where('is_on_leave', true)
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(username) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('sales_id')) {
            $query->where('sales_id', $request->sales_id);
        }

        $perPage = 15;
        if ($request->filled('per_page')) {
            if ($request->per_page === 'all') {
                $perPage = $query->count() > 0 ? $query->count() : 1;
            } else {
                $perPage = (int) $request->per_page;
            }
        }

        $customers = $query->paginate($perPage)->withQueryString();
        $filters = $request->only(['search', 'area_id', 'sales_id', 'per_page']);

        $areas = \App\Models\Area::orderBy('name')->get();
        $users = \App\Models\User::where('is_active', true)->whereHas('customers')->orderBy('name')->get();

        return inertia('Cust/Cuti/Index', compact('user', 'capabilities', 'tenant', 'customers', 'areas', 'users', 'filters'));
    }

    public function store(Request $request, Customer $customer, \App\Services\MikroTik\MikroTikService $mikrotik)
    {
        if ($customer->router_id && $customer->username) {
            $mikrotik->removeSecret($customer->router, $customer->username);
        }

        $customer->update([
            'is_on_leave' => true,
            'status' => 'inactive',
            'leave_start' => now(),
        ]);

        return back()->with('success', "{$customer->name} berhasil dinonaktifkan (Cuti).");
    }

    public function restore(Request $request, Customer $customer)
    {
        $customer->update([
            'is_on_leave' => false,
            'status' => 'active',
            'leave_start' => null,
            'leave_end' => null,
        ]);

        return back()->with('success', "{$customer->name} berhasil dikembalikan ke Daftar Pelanggan.");
    }

    public function destroy(Customer $customer)
    {
        $name = $customer->name;
        $customer->forceDelete();
        return back()->with('success', "{$name} berhasil dihapus permanen.");
    }

    public function batchRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customers,id',
        ]);

        Customer::whereIn('id', $request->ids)->update([
            'is_on_leave' => false,
            'status' => 'active',
            'leave_start' => null,
            'leave_end' => null,
        ]);

        return back()->with('success', count($request->ids) . ' pelanggan berhasil dikembalikan ke Daftar Pelanggan.');
    }

    public function batchDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customers,id',
        ]);

        $count = count($request->ids);
        Customer::whereIn('id', $request->ids)->forceDelete();

        return back()->with('success', "{$count} pelanggan berhasil dihapus permanen.");
    }
}
