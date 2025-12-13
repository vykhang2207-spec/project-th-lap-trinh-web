<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Payout;
use Illuminate\Support\Facades\DB;

class AdminPayoutController extends Controller
{

    // 1. Trang danh sách các GV cần trả lương
    public function index()
    {
        // Lấy danh sách GV có số dư pending > 0
        $teachers = User::where('role', 'teacher')
            ->withSum(['transactions as pending_amount' => function ($q) {
                $q->where('status', 'success')->where('payout_status', 'pending');
            }], 'teacher_earning')
            ->having('pending_amount', '>', 0) // Chỉ hiện người có tiền
            ->get();

        return view('admin.payouts.index', compact('teachers'));
    }

    // 2. Xử lý trả lương cho 1 GV (Quyết toán)
    public function store(Request $request)
    {
        $teacher = User::findOrFail($request->teacher_id);

        // Tính tổng tiền đang nợ GV này
        $pendingAmount = Transaction::whereHas('course', fn($q) => $q->where('teacher_id', $teacher->id))
            ->pendingPayout()
            ->sum('teacher_earning');

        if ($pendingAmount <= 0) return back()->with('error', 'Giáo viên này không có số dư cần trả.');

        DB::transaction(function () use ($teacher, $pendingAmount) {
            // A. Tạo bản ghi Payout
            Payout::create([
                'teacher_id' => $teacher->id,
                'amount' => $pendingAmount,
                'batch_id' => 'PAY_' . now()->format('mY'), // VD: PAY_122025
                'note' => 'Quyết toán doanh thu tháng ' . now()->format('m/Y'),
            ]);

            // B. Đánh dấu tất cả giao dịch cũ là "Đã trả" (completed)
            Transaction::whereHas('course', fn($q) => $q->where('teacher_id', $teacher->id))
                ->pendingPayout()
                ->update(['payout_status' => 'completed']);
        });

        return back()->with('success', 'Đã quyết toán ' . number_format($pendingAmount) . 'đ cho GV ' . $teacher->name);
    }
}
