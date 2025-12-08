<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Chỉ lấy những khóa học đã được Admin duyệt (is_approved = 1)
        $courses = Course::with('teacher') // Lấy thông tin Giảng viên cùng lúc
            ->where('is_approved', 1)
            ->latest() // Khóa học mới nhất lên đầu
            ->paginate(12); // Phân trang 12 khóa học

        return view('welcome', compact('courses'));
    }
}
