<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction; // Quan trọng: Phải import Transaction
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Hiển thị trang thanh toán (Checkout).
     * Route: GET /course/{course}/checkout (Có middleware auth)
     */
    public function create(Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Nếu đã mua rồi thì chuyển hướng về trang chi tiết
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
        // Link callback
        $callbackUrl = route('payment.callback', $course->id);

        // Chuyển hướng sang trang giả lập
        return redirect()->route('momo.simulation', [
            'amount' => $course->price,
            'orderInfo' => 'Thanh toan khoa hoc: ' . $course->title,
            'redirectUrl' => $callbackUrl
        ]);
    }

    /**
     * Xử lý kết quả trả về từ Cổng thanh toán (Callback MoMo).
     * Route: GET /course/{course}/payment-callback
     */
    public function callback(Request $request, Course $course)
    {
        // Kiểm tra đăng nhập (Bắt buộc)
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để hoàn tất giao dịch.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Kiểm tra thành công (resultCode = 0)
        if ($request->resultCode == 0) {

            // Dùng DB Transaction để đảm bảo toàn vẹn dữ liệu
            DB::transaction(function () use ($user, $course) {

                // A. Kiểm tra và tạo Enrollment (Quyền học)
                if (!$user->enrollments()->where('course_id', $course->id)->exists()) {
                    $user->enrollments()->create([
                        'course_id' => $course->id,
                        'user_id' => $user->id,
                    ]);
                }

                // B. Tính toán phân chia doanh thu
                $price = $course->price;
                $tax = $price * 0.10; // Thuế 10%
                $adminFee = $price * 0.20; // Phí sàn 20%
                $teacherEarning = $price - $tax - $adminFee; // GV nhận phần còn lại

                // C. Lưu Transaction (Lịch sử giao dịch & Doanh thu chờ trả)
                Transaction::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'total_amount' => $price,
                    'tax_amount' => $tax,
                    'admin_fee' => $adminFee,
                    'teacher_earning' => $teacherEarning,
                    'payment_method' => 'momo',
                    'status' => 'success',
                    'transaction_id' => 'MOMO_' . time(),

                    // 👇 QUAN TRỌNG CHO LOGIC MỚI:
                    // Đánh dấu là tiền này chưa trả cho GV (Pending Payout)
                    'payout_status' => 'pending',
                ]);
            });

            // 3. Chuyển hướng vào bài học đầu tiên
            $firstLesson = $course->chapters->first()?->lessons->first();

            if ($firstLesson) {
                return redirect()->route('lesson.show', [$course, $firstLesson])
                    ->with('success', 'Thanh toán thành công! Chúc bạn học tốt.');
            } else {
                // Trường hợp khóa học chưa có bài nào
                return redirect()->route('course.show', $course)
                    ->with('success', 'Thanh toán thành công!');
            }
        }

        // 4. Xử lý thất bại
        return redirect()->route('course.show', $course)
            ->with('error', 'Giao dịch thanh toán đã bị hủy.');
    }
}
