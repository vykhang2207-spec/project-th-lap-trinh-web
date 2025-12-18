<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TeacherRevenueController extends Controller
{
    // Thong ke doanh thu giao vien
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Lich su ban khoa hoc
        $transactions = Transaction::whereHas('course', function ($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })
            ->where('status', 'success')
            ->with(['course', 'user'])
            ->latest()
            ->paginate(10, ['*'], 'trans_page');

        // Lich su nhan luong
        $payouts = $user->payouts()
            ->latest()
            ->paginate(5, ['*'], 'payout_page');

        // Tinh tong tien dang cho thanh toan
        $pendingBalance = Transaction::whereHas('course', function ($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })
            ->where('status', 'success')
            ->where('payout_status', 'pending')
            ->sum('teacher_earning');

        // Tinh tong thu nhap tu truoc den nay
        $lifetimeEarnings = Transaction::whereHas('course', function ($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })
            ->where('status', 'success')
            ->sum('teacher_earning');

        // Tinh tong tien da thuc nhan
        $totalPaid = Transaction::whereHas('course', function ($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })
            ->where('status', 'success')
            ->where('payout_status', 'completed')
            ->sum('teacher_earning');

        return view('teacher.revenue.index', compact(
            'transactions',
            'payouts',
            'pendingBalance',
            'lifetimeEarnings',
            'totalPaid'
        ));
    }
}
