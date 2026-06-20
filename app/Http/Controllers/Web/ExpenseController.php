<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    protected $categories = [
        'Operasional', 'Marketing', 'Alat', 'Deviden', 'Konsumsi', 'Lainnya'
    ];

    public function index(Request $request)
    {
        $tenantId = session('tenant_id', $request->user()->tenant_id);
        $bulan = $request->input('bulan', date('Y-m'));
        
        $start = Carbon::parse($bulan . '-01')->startOfMonth();
        $end = Carbon::parse($bulan . '-01')->endOfMonth();

        $query = Expense::where('tenant_id', $tenantId)
            ->whereBetween('expense_date', [$start, $end]);

        // Compute KPI Totals efficiently
        $kpiTotals = (clone $query)->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')->toArray();

        $kpiData = [
            'Total' => array_sum($kpiTotals),
            'Operasional' => $kpiTotals['Operasional'] ?? 0,
            'Marketing' => $kpiTotals['Marketing'] ?? 0,
            'Alat' => $kpiTotals['Alat'] ?? ($kpiTotals['Alat/Material'] ?? 0), // fallback for old data
            'Deviden' => $kpiTotals['Deviden'] ?? 0,
            'Konsumsi' => $kpiTotals['Konsumsi'] ?? 0,
            'Lainnya' => $kpiTotals['Lainnya'] ?? ($kpiTotals['Pajak'] ?? 0), // fallback for old data
        ];

        if ($request->filled('category')) {
            if ($request->category === 'Total') {
                // Do not filter by category if "Total" is clicked
            } else {
                $query->where('category', $request->category);
            }
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $expenses = $query->with('creator:id,name')->orderByDesc('expense_date')->orderByDesc('id')->paginate(20);
        $total = $kpiData['Total'];
        
        $categories = $this->categories;

        return Inertia::render('Reports/Expense', compact('expenses', 'total', 'bulan', 'categories', 'kpiData'));
    }

    public function store(Request $request)
    {
        $tenantId = session('tenant_id', $request->user()->tenant_id);

        $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        Expense::create([
            'tenant_id' => $tenantId,
            'created_by' => $request->user()->id,
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'status' => 'active'
        ]);

        return back()->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
