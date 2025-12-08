<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MomoSimulationController extends Controller
{
    // 1. Hiển thị giao diện quét mã QR giả lập
    public function show(Request $request)
    {
        // Lấy các tham số mà website bán khóa học gửi sang
        $amount = $request->query('amount');
        $orderInfo = $request->query('orderInfo');
        $redirectUrl = $request->query('redirectUrl');

        return view('payment.momo_simulation', compact('amount', 'orderInfo', 'redirectUrl'));
    }

    // 2. Xử lý khi người dùng bấm "Thanh toán thành công"
    public function success(Request $request)
    {
        $redirectUrl = $request->input('redirectUrl');

        // Giả lập MoMo trả về kết quả thành công (resultCode = 0)
        return redirect($redirectUrl . '?resultCode=0&message=ThanhCong');
    }

    // 3. Xử lý khi người dùng bấm "Hủy giao dịch"
    public function cancel(Request $request)
    {
        $redirectUrl = $request->input('redirectUrl');

        // Giả lập MoMo trả về lỗi (resultCode != 0)
        return redirect($redirectUrl . '?resultCode=1006&message=NguoiDungHuy');
    }
}
