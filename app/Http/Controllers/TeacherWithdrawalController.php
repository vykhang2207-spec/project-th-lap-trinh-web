<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Withdrawal;
use App\Models\User;

class TeacherWithdrawalController extends Controller
{
    // Xử lý yêu cầu rút tiền
    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'amount' => 'required|numeric|min:50000', // Rút tối thiểu 50k
            'bank_name' => 'required|string',
            'bank_account_number' => 'required|string',
            'bank_account_name' => 'required|string',
        ]);

        $user = Auth::user();
        $amount = $request->amount;

        // 2. Kiểm tra số dư (Quan trọng!)
        // Lưu ý: Tạm thời chúng ta giả định account_balance đã có tiền 
        // (Ở bước cập nhật doanh thu sau này, khi bán khóa học bạn phải cộng tiền vào account_balance)
        if ($user->account_balance < $amount) {
            return back()->with('error', 'Số dư không đủ để thực hiện giao dịch!');
        }

        // 3. Bắt đầu xử lý (Dùng DB Transaction để an toàn tiền nong)
        DB::beginTransaction();
        try {
            // Mặc định trạng thái là chờ duyệt
            $status = 'pending';
            $autoApproved = false;

            // === LOGIC TỰ ĐỘNG DUYỆT ===
            // Nếu rút dưới 500,000 VNĐ -> Hệ thống tự duyệt luôn
            if ($amount < 500000) {
                $status = 'approved';
                $autoApproved = true;

                // Trừ tiền ngay lập tức
                $user->account_balance -= $amount; // Trừ tiền
                /** @var \App\Models\User $user */
                $user->save(); // Lưu lại vào database
            }

            // Lưu vào bảng withdrawals
            Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_name' => $request->bank_account_name,
                'status' => $status,
                'admin_note' => $autoApproved ? 'Hệ thống tự động duyệt (Hạn mức < 500k)' : null
            ]);

            DB::commit(); // Lưu thay đổi

            if ($autoApproved) {
                return back()->with('success', 'Rút tiền thành công! Tiền đã được chuyển (Giả lập).');
            } else {
                return back()->with('info', 'Yêu cầu đã được gửi. Admin sẽ duyệt trong 24h vì số tiền lớn.');
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Có lỗi thì hoàn tác hết
            return back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại.');
        }
    }
}
