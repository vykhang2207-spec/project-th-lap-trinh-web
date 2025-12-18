<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payout;
use Illuminate\Support\Facades\DB;

class AdminPayoutController extends Controller
{
    // Danh sach giao vien can tra luong
    public function index()
    {
        // Tinh tong tien dang cho (pending) cua moi giao vien
        $teachers = User::where('role', 'teacher')
            ->withSum(['sales as pending_amount' => function ($query) {
                $query->where('payout_status', 'pending');
            }], 'teacher_earning')
            ->orderByDesc('pending_amount')
            ->get();

        // Loc bo giao vien co so du bang 0
        $teachers = $teachers->filter(function ($teacher) {
            return $teacher->pending_amount > 0;
        });

        return view('admin.payouts.index', compact('teachers'));
    }

    // Xu ly thanh toan (Quyet toan)
    public function store(Request $request)
    {
        $request->validate(['teacher_id' => 'required|exists:users,id']);
        $teacher = User::findOrFail($request->teacher_id);

        // Tinh lai so tien can tra
        $pendingAmount = $teacher->sales()
            ->where('payout_status', 'pending')
            ->sum('teacher_earning');

        if ($pendingAmount <= 0) {
            return back()->with('error', 'Giáo viên này không có số dư cần trả (Số dư = 0).');
        }

        DB::transaction(function () use ($teacher, $pendingAmount) {
            // Tao lich su thanh toan
            Payout::create([
                'teacher_id' => $teacher->id,
                'amount'     => $pendingAmount,
                'batch_id'   => 'PAY_' . now()->format('dmY_His'),
                'status'     => 'completed',
                'paid_at'    => now(),
                'note'       => 'Quyết toán doanh thu',
            ]);

            // Cap nhat trang thai giao dich thanh da tra
            $teacher->sales()
                ->where('payout_status', 'pending')
                ->update(['payout_status' => 'completed']);
        });

        return back()->with('success', 'Đã quyết toán thành công ' . number_format($pendingAmount) . 'đ cho GV: ' . $teacher->name);
    }
}
