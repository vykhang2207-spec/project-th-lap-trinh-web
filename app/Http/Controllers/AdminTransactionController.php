<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AdminTransactionController extends Controller
{
    public function index()
    {
        // 1. Lấy tất cả giao dịch thành công (Để hiện bảng bên dưới)
        $transactions = Transaction::with(['user', 'course.teacher'])
            ->where('status', 'success')
            ->latest()
            ->paginate(20);

        // 2. TÍNH TỔNG CÁC SỐ LIỆU THỐNG KÊ (3 thẻ đầu)
        $summary = Transaction::where('status', 'success')
            ->select(
                DB::raw('SUM(total_amount) as total_revenue'),   // Tổng GMV
                DB::raw('SUM(tax_amount) as total_tax'),         // Tổng Thuế
                DB::raw('SUM(admin_fee) as total_admin_profit')  // Lợi nhuận sàn
            )
            ->first();

        // Gán biến (tránh null)
        $totalRevenue = $summary->total_revenue ?? 0;
        $totalTax = $summary->total_tax ?? 0;
        $totalAdminProfit = $summary->total_admin_profit ?? 0;

        // 👇 3. [MỚI] TÍNH TỔNG TIỀN NỢ GIÁO VIÊN (Cho thẻ màu cam)
        // Lấy tổng 'teacher_earning' của các đơn thành công mà 'payout_status' vẫn là 'pending'
        $pendingPayouts = Transaction::where('status', 'success')
            ->where('payout_status', 'pending')
            ->sum('teacher_earning');

        // 4. Truyền đầy đủ dữ liệu sang View
        return view('admin.transactions.index', compact(
            'transactions',
            'totalRevenue',
            'totalTax',
            'totalAdminProfit',
            'pendingPayouts' // 👈 Bắt buộc phải có biến này
        ));
    }
}
