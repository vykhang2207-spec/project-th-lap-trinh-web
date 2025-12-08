<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TeacherRevenueController extends Controller
{
    public function index()
    {
        // 1. Lấy ID giáo viên đang đăng nhập
        $teacherId = Auth::id();

        // 2. Truy vấn: Lấy tất cả giao dịch...
        // ...mà khóa học trong giao dịch đó...
        // ...có 'teacher_id' trùng với giáo viên này.
        $transactions = Transaction::whereHas('course', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
            ->with(['course', 'user']) // Load kèm thông tin Khóa học và Học viên để hiển thị
            ->latest() // Mới nhất lên đầu
            ->paginate(10); // Phân trang

        // 3. Tính tổng doanh thu (Cộng dồn cột amount)
        $totalRevenue = Transaction::whereHas('course', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->sum('teacher_earning');

        return view('teacher.revenue.index', compact('transactions', 'totalRevenue'));
    }
}
