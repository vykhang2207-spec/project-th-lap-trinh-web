<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class AdminCourseController extends Controller
{
    // Lay danh sach tat ca khoa hoc
    public function index()
    {
        // Uu tien hien khoa chua duyet len dau
        $courses = Course::with('teacher')
            ->orderBy('is_approved', 'asc')
            ->latest()
            ->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    // Duyet khoa hoc
    public function approve(Course $course)
    {
        $course->update(['is_approved' => true]);
        return redirect()->back()->with('success', 'Đã duyệt khóa học thành công!');
    }
}
