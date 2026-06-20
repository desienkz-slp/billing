<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\OtherIncome;
use App\Models\Customer;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class OtherIncomeController extends Controller
{
    public function __construct(private PermissionService $permissionService) {}

    public function index(Request $request)
    {
        $user = $request->user()->load('role', 'tenant');
        $capabilities = $this->permissionService->getUserCapabilities($user);
        $tenant = $user->tenant;

        $query = OtherIncome::with(['customer', 'creator'])
            ->orderByDesc('income_date')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($cq) use ($search) {
                    $cq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                       ->orWhereRaw('LOWER(username) LIKE ?', ["%{$search}%"]);
                })->orWhereRaw('LOWER(notes) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('month')) {
            $query->whereRaw("to_char(income_date, 'YYYY-MM') = ?", [$request->month]);
        } else if (!$request->has('month')) {
            $query->whereRaw("to_char(income_date, 'YYYY-MM') = ?", [date('Y-m')]);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $perPage = $query->count() > 0 ? $query->count() : 1;
        }

        $otherIncomes = $query->paginate($perPage)->through(function ($income) {
            return [
                'id' => $income->id,
                'customer_name' => $income->customer ? $income->customer->name : 'Non-Pelanggan (Umum)',
                'category' => $income->category,
                'amount' => $income->amount,
                'income_date' => $income->income_date?->format('Y-m-d'),
                'payment_method' => $income->payment_method,
                'notes' => $income->notes,
                'creator_name' => $income->creator ? $income->creator->name : '-',
            ];
        });

        $filters = $request->only(['search', 'month', 'category', 'per_page']);
        if (!isset($filters['month'])) {
            $filters['month'] = date('Y-m');
        }

        // Dropdown data for modal
        $customers = Customer::orderBy('name')->get(['id', 'name', 'username']);

        $totalAmount = $query->sum('amount');

        return Inertia::render('Reports/OtherIncome/Index', compact('otherIncomes', 'filters', 'capabilities', 'tenant', 'customers', 'totalAmount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'category' => 'required|string|in:Instalasi,Perangkat,Denda,Lainnya',
            'amount' => 'required|integer|min:1',
            'income_date' => 'required|date',
            'payment_method' => 'required|string|max:30',
            'notes' => 'nullable|string|max:255',
        ]);

        $data['tenant_id'] = $request->user()->tenant_id;
        $data['created_by'] = $request->user()->id;

        OtherIncome::create($data);

        return back()->with('success', 'Pendapatan lain berhasil ditambahkan.');
    }

    public function destroy(OtherIncome $otherIncome)
    {
        $otherIncome->delete();
        return back()->with('success', 'Catatan pendapatan berhasil dihapus.');
    }
}
