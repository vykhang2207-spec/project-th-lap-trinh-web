<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    // Hien thi bai hoc
    public function show(Course $course, Lesson $lesson)
    {
        // Kiem tra bai hoc co thuoc khoa hoc khong
        if ($lesson->chapter->course_id !== $course->id) {
            abort(404);
        }

        // Xac dinh bai hoc dau tien
        $firstChapter = $course->chapters->sortBy('order_index')->first();
        $firstLesson = $firstChapter ? $firstChapter->lessons->sortBy('order_index')->first() : null;
        $isTrial = ($firstLesson && $firstLesson->id === $lesson->id);

        // Kiem tra quyen truy cap
        $canView = false;

        if ($isTrial) {
            $canView = true;
        } elseif (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user->role === 'admin') {
                $canView = true;
            } elseif ($user->id === $course->teacher_id) {
                $canView = true;
            } elseif ($user->enrollments()->where('course_id', $course->id)->exists()) {
                $canView = true;
            }
        }

        // Xu ly chuyen huong neu khong co quyen
        if (!$canView) {
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('status', 'Vui lòng đăng nhập để học tiếp.');
            } else {
                return redirect()->route('course.show', $course)
                    ->with('status', 'Bạn cần mua khóa học để truy cập nội dung này!');
            }
        }

        // Load danh sach bai hoc de hien thi sidebar
        $course->load(['chapters.lessons' => function ($query) {
            $query->orderBy('order_index', 'asc');
        }]);

        // Tim bai hoc truoc va sau
        $previousLesson = Lesson::where('chapter_id', $lesson->chapter_id)
            ->where('order_index', '<', $lesson->order_index)
            ->orderBy('order_index', 'desc')
            ->first();

        $nextLesson = Lesson::where('chapter_id', $lesson->chapter_id)
            ->where('order_index', '>', $lesson->order_index)
            ->orderBy('order_index', 'asc')
            ->first();

        return view('courses.learn', compact('course', 'lesson', 'previousLesson', 'nextLesson'));
    }

    // Danh dau hoan thanh bai hoc
    public function complete(Request $request, $id)
    {
        $user = Auth::user();
        $lesson = Lesson::findOrFail($id);

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
            'progress' => $lesson->chapter->course->progress()
        ]);
    }
}
