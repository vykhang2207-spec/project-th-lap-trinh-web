<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show(Course $course, Lesson $lesson)
    {
        // 1. Validate bài học thuộc khóa học
        if ($lesson->chapter->course_id !== $course->id) {
            abort(404);
        }

        // 2. 👇 CHECK QUYỀN: Người dùng phải mua khóa học rồi mới được xem
        $user = request()->user();

        // Nếu chưa mua khóa học NÀY
        if (!$user->enrollments()->where('course_id', $course->id)->exists()) {
            // Và bài học này KHÔNG PHẢI là bài học thử (bài đầu tiên)
            // (Giả sử 2 bài đầu tiên có order_index 1 và 2 là học thử)
            if ($lesson->order_index > 2) {
                return redirect()->route('course.show', $course)
                    ->with('error', 'Bạn cần mua khóa học để xem tiếp nội dung này.');
            }
        }

        // ... (Phần code cũ lấy danh sách bài học giữ nguyên) ...
        $course->load(['chapters.lessons' => function ($query) {
            $query->orderBy('order_index', 'asc');
        }]);

        // ... (Code lấy next/previous lesson giữ nguyên) ...
        $previousLesson = Lesson::where('chapter_id', $lesson->chapter_id)->where('order_index', '<', $lesson->order_index)->orderBy('order_index', 'desc')->first();
        $nextLesson = Lesson::where('chapter_id', $lesson->chapter_id)->where('order_index', '>', $lesson->order_index)->orderBy('order_index', 'asc')->first();

        return view('courses.learn', compact('course', 'lesson', 'previousLesson', 'nextLesson'));
    }
}
