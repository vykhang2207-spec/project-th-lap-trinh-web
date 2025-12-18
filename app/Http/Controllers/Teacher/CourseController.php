<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Lesson;

class CourseController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    // Hien thi danh sach khoa hoc cua giang vien
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $courses = $user->courses()->latest()->paginate(10);
        return view('teacher.courses.index', compact('courses'));
    }

    // Hien thi form them khoa hoc
    public function create()
    {
        $this->authorize('create', Course::class);
        return view('teacher.courses.create');
    }

    // Luu khoa hoc moi
    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ], [
            'title.required' => 'Vui lòng nhập tên khóa học.',
            'price.min' => 'Giá tiền phải lớn hơn 0.',
            'image.image' => 'File phải là định dạng ảnh (jpg, png, v.v.).',
        ]);

        // Upload anh len storage
        $imagePath = $request->file('image')->store('courses', 'public');

        Course::create([
            'teacher_id' => Auth::id(),
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'image_path' => $imagePath,
            'is_approved' => false,
        ]);

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Khóa học đã được đăng thành công và đang chờ Admin duyệt!');
    }

    // Hien thi form sua khoa hoc
    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        return view('teacher.courses.edit', compact('course'));
    }

    // Cap nhat khoa hoc
    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Xu ly anh moi neu co
        if ($request->hasFile('image')) {
            if ($course->image_path) {
                Storage::disk('public')->delete($course->image_path);
            }
            $imagePath = $request->file('image')->store('courses', 'public');
        } else {
            $imagePath = $course->image_path;
        }

        $course->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'image_path' => $imagePath,
            'is_approved' => false, // Reset trang thai duyet
        ]);

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Khóa học đã được cập nhật và đang chờ Admin duyệt lại!');
    }

    // Xoa khoa hoc
    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        if ($course->image_path) {
            Storage::disk('public')->delete($course->image_path);
        }

        $course->delete();

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Khóa học đã được xóa thành công!');
    }

    // Quan ly noi dung chuong va bai hoc
    public function contentIndex(Course $course)
    {
        $this->authorize('update', $course);

        $course->load(['chapters' => function ($query) {
            $query->orderBy('order_index', 'asc');
        }, 'chapters.lessons' => function ($query) {
            $query->orderBy('order_index', 'asc');
        }]);

        return view('teacher.courses.content-index', compact('course'));
    }

    // Them chuong moi
    public function storeChapter(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
        ], [
            'title.required' => 'Vui lòng nhập tên chương.',
        ]);

        $maxOrder = $course->chapters()->max('order_index');
        $newOrder = $maxOrder ? $maxOrder + 1 : 1;

        \App\Models\Chapter::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'order_index' => $newOrder,
        ]);

        return redirect()->route('teacher.courses.content.index', $course)
            ->with('success', 'Đã thêm chương mới thành công!');
    }

    // Them bai hoc moi
    public function storeLesson(Request $request, \App\Models\Chapter $chapter)
    {
        $this->authorize('update', $chapter->course);

        $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|string',
            'is_preview' => 'boolean',
        ]);

        $maxOrder = $chapter->lessons()->max('order_index');
        $newOrder = $maxOrder ? $maxOrder + 1 : 1;

        $chapter->lessons()->create([
            'title' => $request->title,
            'video_url' => $request->video_url,
            'duration' => $request->duration ?? '00:00',
            'is_preview' => $request->boolean('is_preview'),
            'order_index' => $newOrder,
        ]);

        return back()->with('success', 'Đã thêm bài học thành công!');
    }

    // Cap nhat bai hoc
    public function updateLesson(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson->chapter->course);

        $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|string',
            'is_preview' => 'boolean',
        ]);

        $lesson->update([
            'title' => $request->title,
            'video_url' => $request->video_url,
            'duration' => $request->duration,
            'is_preview' => $request->boolean('is_preview'),
        ]);

        return back()->with('success', 'Đã cập nhật bài học!');
    }

    // Xoa bai hoc
    public function destroyLesson(Lesson $lesson)
    {
        $this->authorize('update', $lesson->chapter->course);
        $lesson->delete();
        return back()->with('success', 'Đã xóa bài học!');
    }

    // Xem chi tiet khoa hoc (cua giang vien)
    public function show(Course $course)
    {
        $course->loadCount(['likes', 'dislikes', 'comments']);

        $course->load(['lessons' => function ($query) {
            $query->withCount('viewers');
        }]);

        $totalViews = $course->lessons->sum('viewers_count');
        $comments = $course->comments()->with('user')->latest()->get();

        return view('teacher.courses.show', compact('course', 'comments', 'totalViews'));
    }
}
