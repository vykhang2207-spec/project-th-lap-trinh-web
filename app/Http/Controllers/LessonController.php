<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function show(Course $course, Lesson $lesson)
    {
        // 1. Validate: Bài học phải thuộc khóa học này
        if ($lesson->chapter->course_id !== $course->id) {
            abort(404);
        }

        // ====================================================
        // 👇 LOGIC 1: XÁC ĐỊNH BÀI HỌC THỬ (DYNAMIC)
        // ====================================================
        // Lấy chương đầu tiên -> Lấy bài học đầu tiên của chương đó
        $firstChapter = $course->chapters->sortBy('order_index')->first();
        // Nếu bạn không có cột order_index thì đổi thành sortBy('id')

        $firstLesson = $firstChapter ? $firstChapter->lessons->sortBy('order_index')->first() : null;

        // Kiểm tra xem bài hiện tại có phải là bài đầu tiên không?
        $isTrial = ($firstLesson && $firstLesson->id === $lesson->id);


        // ====================================================
        // 👇 LOGIC 2: PHÂN QUYỀN TRUY CẬP (GATEKEEPER)
        // ====================================================
        $canView = false;

        // Trường hợp A: Nếu là bài học thử -> Ai cũng xem được (kể cả chưa login)
        if ($isTrial) {
            $canView = true;
        }

        // Trường hợp B: Nếu đã đăng nhập -> Kiểm tra kỹ hơn
        elseif (Auth::check()) {
            /** @var \App\Models\User $user */ // 👈 THÊM DÒNG NÀY
            $user = Auth::user();

            // 1. Là Admin -> Xem hết (Quyền lực tối cao)
            if ($user->role === 'admin') {
                $canView = true;
            }
            // 2. Là Giảng viên của chính khóa này -> Xem hết
            elseif ($user->id === $course->teacher_id) {
                $canView = true;
            }
            // 3. Là Học viên đã mua khóa này -> Xem hết
            elseif ($user->enrollments()->where('course_id', $course->id)->exists()) {
                $canView = true;
            }
        }

        // ====================================================
        // 👇 LOGIC 3: XỬ LÝ KẾT QUẢ
        // ====================================================
        if (!$canView) {
            // Nếu chưa đăng nhập -> Đẩy về login
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('status', 'Vui lòng đăng nhập để học tiếp.');
            }
            // Nếu đã đăng nhập nhưng chưa mua -> Đẩy về trang chi tiết khóa học để mua
            else {
                return redirect()->route('course.show', $course)
                    ->with('status', 'Bạn cần mua khóa học để truy cập nội dung này!');
            }
        }

        // ====================================================
        // 👇 LOGIC 4: CHUẨN BỊ DỮ LIỆU VIEW (SIDEBAR & NAV)
        // ====================================================

        // Load danh sách bài học để hiện Sidebar bên trái
        // Sắp xếp theo order_index để hiển thị đúng thứ tự
        $course->load(['chapters.lessons' => function ($query) {
            $query->orderBy('order_index', 'asc');
        }]);

        // Logic nút Next/Previous (Tìm bài liền kề trong cùng 1 chương)
        // Lưu ý: Logic này chỉ tìm trong cùng 1 chương. 
        // Nếu muốn Next sang chương mới thì cần logic phức tạp hơn chút, nhưng tạm thời thế này là ổn.
        $previousLesson = Lesson::where('chapter_id', $lesson->chapter_id)
            ->where('order_index', '<', $lesson->order_index)
            ->orderBy('order_index', 'desc')
            ->first();

        $nextLesson = Lesson::where('chapter_id', $lesson->chapter_id)
            ->where('order_index', '>', $lesson->order_index)
            ->orderBy('order_index', 'asc')
            ->first();

        // View này bạn cần tạo file resources/views/courses/learn.blade.php nhé (khác với course.show)
        return view('courses.learn', compact('course', 'lesson', 'previousLesson', 'nextLesson'));
    }
    public function complete(Request $request, $id)
    {
        $user = Auth::user();
        $lesson = Lesson::findOrFail($id);

        // Lưu vào bảng lesson_views (dùng updateOrCreate để không bị trùng)
        \Illuminate\Support\Facades\DB::table('lesson_views')->updateOrInsert(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'last_viewed_at' => now()
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Đã đánh dấu hoàn thành!',
            'progress' => $lesson->chapter->course->progress() // Trả về tiến độ mới luôn nếu cần
        ]);
    }
}
