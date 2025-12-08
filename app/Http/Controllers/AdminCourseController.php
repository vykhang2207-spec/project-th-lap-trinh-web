<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class AdminCourseController extends Controller
{
    public function index()
    {
        // Admin lấy TẤT CẢ khóa học (kèm thông tin giáo viên)
        // Sắp xếp khóa mới nhất lên đầu, hoặc khóa chờ duyệt (pending) lên đầu
        $courses = Course::with('teacher')
            ->orderBy('is_approved', 'asc') // Ưu tiên hiện khóa chưa duyệt trước
            ->latest()
            ->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }
    // app/Http/Controllers/AdminCourseController.php

    public function approve(Course $course)
    {
        // Cập nhật trạng thái
        $course->update(['is_approved' => true]);

        // Quay lại trang danh sách và báo thành công
        return redirect()->back()->with('success', 'Đã duyệt khóa học thành công!');
    }
}
