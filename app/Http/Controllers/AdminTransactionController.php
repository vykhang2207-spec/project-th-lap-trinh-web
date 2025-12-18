<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AdminTransactionController extends Controller
{
    // Thong ke giao dich
    public function index()
    {
        // Lay danh sach giao dich thanh cong
        $transactions = Transaction::with(['user', 'course.teacher'])
            ->where('status', 'success')
            ->latest()
            ->paginate(20);

        // Tinh tong doanh thu, thue va loi nhuan admin
        $summary = Transaction::where('status', 'success')
            ->select(
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(admin_fee) as total_admin_profit')
            )
            ->first();

        $totalRevenue = $summary->total_revenue ?? 0;
        $totalTax = $summary->total_tax ?? 0;
        $totalAdminProfit = $summary->total_admin_profit ?? 0;

        // Tinh tong tien dang no giao vien (chua payout)
        $pendingPayouts = Transaction::where('status', 'success')
            ->where('payout_status', 'pending')
            ->sum('teacher_earning');

        return view('admin.transactions.index', compact(
            'transactions',
            'totalRevenue',
            'totalTax',
            'totalAdminProfit',
            'pendingPayouts'
        ));
    }
}
