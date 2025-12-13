<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TeacherRevenueController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Lịch sử Bán khóa học (Để GV biết mình bán được gì)
        $transactions = Transaction::whereHas('course', function ($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })
            ->where('status', 'success')
            ->with(['course', 'user'])
            ->latest()
            ->paginate(10, ['*'], 'trans_page');

        // 2. Lịch sử Nhận Lương (Payouts)
        // Sử dụng relationship payouts() mới tạo trong User Model
        $payouts = $user->payouts()
            ->latest()
            ->paginate(5, ['*'], 'payout_page');

        // 3. CÁC CON SỐ THỐNG KÊ

        // A. Doanh thu chờ thanh toán (Pending Balance)
        $pendingBalance = Transaction::whereHas('course', function ($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })
            ->where('status', 'success')
            ->where('payout_status', 'pending')
            ->sum('teacher_earning');

        // B. Tổng thu nhập trọn đời (Lifetime Earnings)
        $lifetimeEarnings = Transaction::whereHas('course', function ($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })
            ->where('status', 'success')
            ->sum('teacher_earning');

        // C. Tổng tiền đã được Admin trả (Total Paid)
        $totalPaid = $user->payouts()
            ->where('status', 'completed')
            ->sum('amount');

        return view('teacher.revenue.index', compact(
            'transactions',
            'payouts',
            'pendingBalance',
            'lifetimeEarnings',
            'totalPaid'
        ));
    }
}
