<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // Hien thi trang thanh toan
    public function create(Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return redirect()->route('course.show', $course);
        }

        return view('payment.checkout', compact('course'));
    }

    // Tao link thanh toan Momo
    public function store(Request $request, Course $course)
    {
        $callbackUrl = route('payment.callback', $course->id);

        return redirect()->route('momo.simulation', [
            'amount' => $course->price,
            'orderInfo' => 'Thanh toan khoa hoc: ' . $course->title,
            'redirectUrl' => $callbackUrl
        ]);
    }

    // Xu ly callback tu Momo
    public function callback(Request $request, Course $course)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để hoàn tất giao dịch.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->resultCode == 0) {
            DB::transaction(function () use ($user, $course) {

                // Tao quyen hoc (enrollment)
                if (!$user->enrollments()->where('course_id', $course->id)->exists()) {
                    $user->enrollments()->create([
                        'course_id' => $course->id,
                        'user_id' => $user->id,
                    ]);
                }

                // Tinh toan doanh thu
                $price = $course->price;
                $tax = $price * 0.10;
                $adminFee = $price * 0.20;
                $teacherEarning = $price - $tax - $adminFee;

                // Luu giao dich
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
                    'payout_status' => 'pending',
                ]);
            });

            // Chuyen huong vao bai hoc
            $firstLesson = $course->chapters->first()?->lessons->first();

            if ($firstLesson) {
                return redirect()->route('lesson.show', [$course, $firstLesson])
                    ->with('success', 'Thanh toán thành công! Chúc bạn học tốt.');
            } else {
                return redirect()->route('course.show', $course)
                    ->with('success', 'Thanh toán thành công!');
            }
        }

        return redirect()->route('course.show', $course)
            ->with('error', 'Giao dịch thanh toán đã bị hủy.');
    }
}
