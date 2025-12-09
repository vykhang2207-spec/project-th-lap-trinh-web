<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TeacherRevenueController extends Controller
{
    public function index()
    {
        // 1. Lấy User hiện tại (Giáo viên)
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Lấy Lịch sử Giao dịch (TIỀN VÀO - Bán khóa học)
        $transactions = Transaction::whereHas('course', function ($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })
            ->where('status', 'success') // Chỉ lấy giao dịch thành công
            ->with(['course', 'user'])   // Load kèm thông tin
            ->latest()
            // 👇 QUAN TRỌNG: Đặt tên biến page là 'trans_page' để không trùng với bảng rút tiền
            ->paginate(5, ['*'], 'trans_page');

        // 3. Lấy Lịch sử Rút tiền (TIỀN RA)
        // (Sử dụng relationship withdrawals đã khai báo trong User model)
        $withdrawals = $user->withdrawals()
            ->latest()
            // 👇 QUAN TRỌNG: Đặt tên biến page là 'withdraw_page'
            ->paginate(5, ['*'], 'withdraw_page');

        // 4. Các con số thống kê

        // A. Số dư khả dụng (Lấy trực tiếp từ DB User)
        $currentBalance = $user->account_balance;

        // B. Tổng thu nhập trọn đời (Tổng teacher_earning của các đơn thành công)
        $totalEarned = Transaction::whereHas('course', function ($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })->where('status', 'success')->sum('teacher_earning');

        // C. Tổng số tiền đã rút thành công
        $totalWithdrawn = $user->withdrawals()
            ->where('status', 'approved')
            ->sum('amount');

        // 5. Trả về View với đầy đủ dữ liệu
        // Lưu ý: View cần sửa lại để nhận các biến mới này (như mình đã gửi ở bước trước)
        return view('teacher.revenue.index', compact(
            'transactions',
            'withdrawals',
            'currentBalance',
            'totalEarned',
            'totalWithdrawn'
        ));
    }
}
