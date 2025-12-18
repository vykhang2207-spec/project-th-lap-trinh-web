<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MomoSimulationController extends Controller
{
    // Hien thi trang thanh toan gia lap
    public function show(Request $request)
    {
        $amount = $request->query('amount');
        $orderInfo = $request->query('orderInfo');
        $redirectUrl = $request->query('redirectUrl');

        return view('payment.momo_simulation', compact('amount', 'orderInfo', 'redirectUrl'));
    }

    // Xu ly thanh toan thanh cong
    public function success(Request $request)
    {
        $redirectUrl = $request->input('redirectUrl');
        return redirect($redirectUrl . '?resultCode=0&message=ThanhCong');
    }

    // Xu ly huy thanh toan
    public function cancel(Request $request)
    {
        $redirectUrl = $request->input('redirectUrl');
        return redirect($redirectUrl . '?resultCode=1006&message=NguoiDungHuy');
    }
}
