<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    // Trang chủ (Có tìm kiếm)
    public function index(Request $request)
    {
        $userId = Auth::id();

        // 1. Khởi tạo Query cơ bản
        $query = Course::with('teacher')
            ->withCount(['enrollments', 'likes', 'dislikes'])
            // Lấy trạng thái reaction của user hiện tại (để tô màu nút)
            ->with(['reactions' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->where('is_approved', 1);

        // 2. 👇 THÊM LOGIC TÌM KIẾM (SEARCH)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%") // Tìm theo tên khóa học
                    ->orWhereHas('teacher', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%"); // Tìm theo tên giảng viên
                    });
            });
        }

        // 3. Lấy kết quả & Phân trang
        $courses = $query->latest()->paginate(12);

        // Giữ lại từ khóa tìm kiếm trên URL khi bấm sang trang 2, 3...
        $courses->appends(['search' => $request->search]);

        return view('welcome', compact('courses'));
    }

    // Trang hồ sơ giảng viên
    public function teacherProfile($id)
    {
        $userId = Auth::id();

        // Tìm giảng viên
        $teacher = User::where('id', $id)->where('role', 'teacher')->firstOrFail();

        // Lấy danh sách khóa học của giảng viên đó
        $courses = $teacher->courses()
            ->where('is_approved', 1)
            ->withCount(['enrollments', 'likes', 'dislikes'])
            // 👇 MÌNH THÊM CÁI NÀY VÀO ĐÂY LUÔN ĐỂ NÚT LIKE BÊN TRANG PROFILE CŨNG HOẠT ĐỘNG
            ->with(['reactions' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->latest()
            ->paginate(12);

        return view('teacher.profile', compact('teacher', 'courses'));
    }
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('query');

        if (!$query) {
            return response()->json([]);
        }

        // Tìm kiếm khóa học theo tên (Lấy tối đa 5 kết quả cho nhẹ)
        $courses = Course::where('title', 'like', "%{$query}%")
            ->where('is_approved', 1)
            ->with('teacher') // Lấy thêm tên giáo viên
            ->select('id', 'title', 'image_path', 'teacher_id') // Chỉ lấy cột cần thiết
            ->take(5)
            ->get();

        return response()->json($courses);
    }
}
