<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Hiển thị chi tiết khóa học (Trang Landing Page của khóa học)
     * Route: GET /course/{course}
     * Ai cũng xem được (Public) nhưng có kiểm soát
     */
    public function show($id)
    {
        // 1. Tìm khóa học và lấy kèm dữ liệu (Eager Loading)
        // Lấy luôn chapters và lessons để hiển thị danh sách bài học
        $course = Course::with(['teacher', 'chapters.lessons'])->findOrFail($id);

        // 2. LOGIC KIỂM TRA QUYỀN XEM (Quan trọng)
        $canView = false;

        // Trường hợp 1: Khóa học đã duyệt -> Ai cũng xem được
        if ($course->is_approved) {
            $canView = true;
        }

        // Trường hợp 2: Chưa duyệt, nhưng người xem là Admin hoặc Tác giả
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin' || $user->id === $course->teacher_id) {
                $canView = true;
            }
        }

        // Nếu không thỏa mãn điều kiện nào -> Chặn (Lỗi 404)
        if (!$canView) {
            abort(404, 'Khóa học này chưa được công khai.');
        }

        // 3. Trả về view
        return view('courses.show', compact('course'));
    }
}
