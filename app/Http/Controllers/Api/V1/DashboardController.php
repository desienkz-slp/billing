<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Setoran;
use App\Models\Customer;
use App\Models\MonthlyBalance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Start and end of requested month
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // 1. Calculate Wajib Setor
        // Total penerimaan dari payment
        $totalPayments = Payment::where(function($q) use ($user) {
                $q->where('collected_by', $user->id)
                  ->orWhereHas('customer', function($q2) use ($user) {
                      $q2->where('sales_id', $user->id);
                  });
            })
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        // Total pengeluaran
        $totalExpenses = Expense::where('created_by', $user->id)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Total Setoran
        $totalSetoran = \App\Models\Deposit::where('sales_id', $user->id)
            ->whereBetween('deposit_date', [$startDate, $endDate])
            ->sum('amount');

        // Sederhanakan kalkulasi Wajib Setor:
        $wajibSetor = max(0, $totalPayments - $totalExpenses - $totalSetoran);

        // 2. Chart Status Tagihan (Bulan ini)
        // Kita hitung dari MonthlyBalance untuk periode yang diminta
        $periodString = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT);
        
        $sudahBayar = MonthlyBalance::where('period', $periodString)->where('status', 'paid')->count();
        $belumBayar = MonthlyBalance::where('period', $periodString)->where('status', 'unpaid')->count();
        
        // Telat: Unpaid dan period lewat
        $telat = 0;
        if ($startDate->isPast() && !Carbon::now()->isSameMonth($startDate)) {
            $telat = $belumBayar;
            $belumBayar = 0; // Jika bulan lalu dan belum bayar, anggap telat
        }

        $isolir = Customer::where('is_isolated', true)->count();

        // Check if user has permission to collect money
        $showWajibSetor = false;
        if ($user->fee_persen > 0 || $user->fee_fix > 0 || $user->hasPermission('bayar') || $user->isSuperAdmin() || $user->isSystemAdmin()) {
            $showWajibSetor = true;
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'show_wajib_setor' => $showWajibSetor,
                'wajib_setor' => $wajibSetor,
                'total_payments' => $totalPayments,
                'total_expenses' => $totalExpenses,
                'total_setoran' => $totalSetoran,
                'chart_tagihan' => [
                    'sudah_bayar' => $sudahBayar,
                    'belum_bayar' => $belumBayar,
                    'telat' => $telat,
                    'isolir' => $isolir
                ]
            ]
        ]);
    }
}
