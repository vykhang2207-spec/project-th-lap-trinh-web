<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class AdminTransactionController extends Controller
{
    public function index()
    {
        // Lấy tất cả giao dịch thành công
        $transactions = Transaction::with(['user', 'course.teacher'])
            ->where('status', 'success') // Chỉ tính đơn thành công
            ->latest()
            ->paginate(20);

        // TÍNH TỔNG SỐ LIỆU ĐỂ ADMIN SƯỚNG MẮT
        $totalRevenue = Transaction::where('status', 'success')->sum('total_amount'); // Tổng dòng tiền qua sàn
        $totalTax = Transaction::where('status', 'success')->sum('tax_amount');       // Tổng tiền phải nộp thuế
        $totalAdminProfit = Transaction::where('status', 'success')->sum('admin_fee'); // LỢI NHUẬN CỦA ADMIN

        return view('admin.transactions.index', compact(
            'transactions',
            'totalRevenue',
            'totalTax',
            'totalAdminProfit'
        ));
    }
}
