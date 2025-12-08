<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Enrollment;

class EnrollmentController extends Controller
{
    public function store(Request $request, Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Kiểm tra xem đã mua chưa
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return redirect()->route('course.show', $course)
                ->with('error', 'Bạn đã sở hữu khóa học này rồi!');
        }

        // 2. Tạo bản ghi đăng ký (Giả lập mua thành công ngay lập tức)
        $user->enrollments()->create([
            'course_id' => $course->id,
            // Nếu bảng enrollment của bạn có cột 'price' hoặc 'status', hãy thêm vào đây
            // 'amount' => $course->price, 
        ]);

        // 3. Chuyển hướng người dùng vào bài học đầu tiên
        $firstLesson = $course->chapters->first()->lessons->first();

        return redirect()->route('lesson.show', [$course, $firstLesson])
            ->with('success', 'Đăng ký thành công! Chúc bạn học tốt.');
    }
}
