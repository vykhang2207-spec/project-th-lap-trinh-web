<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment; // Đảm bảo đã import Model Enrollment
use App\Models\User; // Đảm bảo đã import Model User

class PaymentController extends Controller
{
    /**
     * Hiển thị trang thanh toán (Checkout).
     * Route: GET /course/{course}/checkout (Có middleware auth)
     */
    public function create(Course $course)
    {
        // Khai báo kiểu dữ liệu cho IDE (Intelephense)
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Nếu đã mua rồi thì chuyển hướng về trang chi tiết (đã có nút "Vào học")
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return redirect()->route('course.show', $course);
        }

        return view('payment.checkout', compact('course'));
    }

    /**
     * Xử lý tạo link thanh toán MoMo (Chuyển sang trang GIẢ LẬP).
     * Route: POST /course/{course}/pay (Có middleware auth)
     */
    public function store(Request $request, Course $course)
    {
        // 🚨 CHÚ Ý: Hàm này chỉ chuyển hướng sang cổng thanh toán GIẢ LẬP

        // Link mà MoMo (giả) sẽ gọi lại sau khi thanh toán xong
        $callbackUrl = route('payment.callback', $course->id);

        // Chuyển hướng sang trang giả lập kèm thông tin
        return redirect()->route('momo.simulation', [
            'amount' => $course->price,
            'orderInfo' => 'Thanh toan khoa hoc: ' . $course->title,
            'redirectUrl' => $callbackUrl
        ]);
    }

    /**
     * Xử lý kết quả trả về từ Cổng thanh toán (Callback MoMo).
     * Route: GET /course/{course}/payment-callback (KHÔNG có middleware auth)
     */
    public function callback(Request $request, Course $course)
    {
        // 🚨 FIX LỖI RUNTIME: Bắt buộc kiểm tra Auth vì route này không có middleware auth
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để hoàn tất giao dịch MoMo.');
        }
        
        // Khai báo kiểu dữ liệu cho IDE
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Kiểm tra mã lỗi trả về từ trang giả lập (resultCode = 0 là thành công)
        if ($request->resultCode == 0) {

            // 2. Tạo bản ghi đăng ký nếu chưa tồn tại
            if (!$user->enrollments()->where('course_id', $course->id)->exists()) {
                $user->enrollments()->create([
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                ]);
            }

            // 3. Chuyển hướng vào bài học đầu tiên
            $firstLesson = $course->chapters->first()->lessons->first();

            return redirect()->route('lesson.show', [$course, $firstLesson])
                ->with('success', 'Thanh toán MoMo thành công! Chào mừng bạn.');
        }

        // 4. Xử lý Thất bại/Hủy
        return redirect()->route('course.show', $course)
            ->with('error', 'Thanh toán thất bại hoặc bị hủy.');
    }
}
