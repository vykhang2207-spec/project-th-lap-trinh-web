<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Cần thiết cho upload ảnh
use App\Models\Lesson; //
class CourseController extends Controller
{
    // Fix lỗi Authorize: Tự động gắn Trait kiểm tra quyền vào Controller
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    /**
     * Hiển thị danh sách khóa học của Giảng viên hiện tại
     * Route: GET /teacher/courses
     */
    public function index()
    {
    // 👇 THÊM DÒNG NÀY VÀO ĐÂY
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $courses = $user->courses()->latest()->paginate(10);

        return view('teacher.courses.index', compact('courses'));
    }
    /**
     * Hiển thị form để thêm khóa học mới
     * Route: GET /teacher/courses/create
     */
    public function create()
    {
        // FIX LỖI: Sử dụng $this->authorize để kiểm tra quyền
        // Nếu user không phải teacher/admin, sẽ bị chặn ở đây (403 Forbidden)
        $this->authorize('create', Course::class);

        return view('teacher.courses.create');
    }

    /**
     * Xử lý lưu trữ Khóa học mới
     * Route: POST /teacher/courses
     */
    public function store(Request $request)
    {
        // 1. Kiểm tra quyền
        $this->authorize('create', Course::class);

        // 2. Xác thực dữ liệu
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'title.required' => 'Vui lòng nhập tên khóa học.',
            'price.min' => 'Giá tiền phải lớn hơn 0.',
            'image.image' => 'File phải là định dạng ảnh (jpg, png, v.v.).',
        ]);

        // 3. Xử lý Upload Ảnh lên Storage
        $imagePath = $request->file('image')->store('courses', 'public');

        // 4. Lưu Khóa học vào Database
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

    public function edit(Course $course)
    {
        // 1. Kiểm tra quyền: Chỉ Giảng viên sở hữu hoặc Admin mới được sửa
        $this->authorize('update', $course);

        // 2. Trả về form sửa
        return view('teacher.courses.edit', compact('course'));
    }

    /**
     * Xử lý Cập nhật Khóa học
     * Route: PUT/PATCH /teacher/courses/{course}
     */
    public function update(Request $request, Course $course)
    {
        // 1. Kiểm tra quyền
        $this->authorize('update', $course);

        // 2. Xác thực dữ liệu (Hình ảnh là tùy chọn khi sửa)
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Nullable: Không bắt buộc
        ]);

        // 3. Xử lý Upload/Xóa Ảnh mới (Nếu có)
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ khỏi storage nếu nó tồn tại
            if ($course->image_path) {
                Storage::disk('public')->delete($course->image_path);
            }
            // Lưu ảnh mới
            $imagePath = $request->file('image')->store('courses', 'public');
        } else {
            // Giữ lại ảnh cũ
            $imagePath = $course->image_path;
        }

        // 4. Cập nhật vào Database
        $course->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'image_path' => $imagePath,
            // Chú ý: Cập nhật khóa học sẽ đưa nó về trạng thái CHỜ DUYỆT lại
            'is_approved' => false,
        ]);

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Khóa học đã được cập nhật và đang chờ Admin duyệt lại!');
    }
    /**
     * Xử lý Xóa Khóa học
     * Route: DELETE /teacher/courses/{course}
     */
    public function destroy(Course $course)
    {
        // 1. Kiểm tra quyền (Chỉ Giảng viên sở hữu/Admin)
        $this->authorize('delete', $course); // Sử dụng Policy CoursePolicy

        // 2. Xóa file ảnh liên quan khỏi storage
        if ($course->image_path) {
            // Hàm delete này sẽ xóa file khỏi thư mục storage/app/public/courses
            Storage::disk('public')->delete($course->image_path);
        }

        // 3. Xóa bản ghi Khóa học
        // Các Chapter, Lesson, LessonView và Enrollment liên quan
        // sẽ tự động bị xóa (cascade) nhờ thiết lập trong Migration.
        $course->delete();

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Khóa học đã được xóa thành công!');
    }
    /**
     * Hiển thị giao diện quản lý Chương và Bài học (Content Builder)
     */
    public function contentIndex(Course $course)
    {
        // Kiểm tra quyền: Chỉ Giảng viên sở hữu hoặc Admin mới được quản lý nội dung
        $this->authorize('update', $course);

        // Load tất cả Chương và Bài học theo thứ tự đã được fix
        $course->load(['chapters' => function ($query) {
            $query->orderBy('order_index', 'asc');
        }, 'chapters.lessons' => function ($query) {
            $query->orderBy('order_index', 'asc');
        }]);

        return view('teacher.courses.content-index', compact('course'));
    }
    /**
     * Xử lý lưu Chương mới
     */
    public function storeChapter(Request $request, Course $course)
    {
        // 1. Kiểm tra quyền
        $this->authorize('update', $course);

        // 2. Validate dữ liệu
        $request->validate([
            'title' => 'required|string|max:255',
        ], [
            'title.required' => 'Vui lòng nhập tên chương.',
        ]);

        // 3. Tính số thứ tự (order_index)
        // Lấy order lớn nhất hiện tại + 1, nếu chưa có thì là 1
        $maxOrder = $course->chapters()->max('order_index');
        $newOrder = $maxOrder ? $maxOrder + 1 : 1;

        // 4. Tạo chương mới (Sử dụng Model Chapter)
        // Đảm bảo bạn đã use App\Models\Chapter; ở đầu file Controller
        \App\Models\Chapter::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'order_index' => $newOrder,
        ]);

        // 5. Quay lại trang quản lý nội dung
        return redirect()->route('teacher.courses.content.index', $course)
            ->with('success', 'Đã thêm chương mới thành công!');
    }
    /**
     * Xử lý lưu Bài học mới vào Chương
     * Route: POST /chapters/{chapter}/lessons
     */
    public function storeLesson(Request $request, \App\Models\Chapter $chapter)
    {
        // 1. Kiểm tra quyền sở hữu khóa học thông qua chương
        $this->authorize('update', $chapter->course);

        // 2. Validate dữ liệu
        $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|url', // Link video (Youtube/Vimeo...)
            'duration' => 'nullable|string', // Thời lượng (VD: 10:05)
            'is_preview' => 'boolean', // Cho phép xem thử không?
        ]);

        // 3. Tính số thứ tự (order_index)
        $maxOrder = $chapter->lessons()->max('order_index');
        $newOrder = $maxOrder ? $maxOrder + 1 : 1;

        // 4. Lưu Bài học
        $chapter->lessons()->create([
            'title' => $request->title,
            'video_url' => $request->video_url,
            'duration' => $request->duration ?? '00:00',
            'is_preview' => $request->boolean('is_preview'),
            'order_index' => $newOrder,
        ]);

        return back()->with('success', 'Đã thêm bài học thành công!');
    }
    public function updateLesson(Request $request, Lesson $lesson)
    {
        // 1. Kiểm tra quyền (bài học này có thuộc khóa học của ông thầy này không)
        $this->authorize('update', $lesson->chapter->course);

        // 2. Validate dữ liệu
        $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|string',
            'is_preview' => 'boolean',
        ]);

        // 3. Cập nhật
        $lesson->update([
            'title' => $request->title,
            'video_url' => $request->video_url,
            'duration' => $request->duration,
            'is_preview' => $request->boolean('is_preview'),
        ]);

        return back()->with('success', 'Đã cập nhật bài học!');
    }

    /**
     * Xóa bài học
     */
    public function destroyLesson(Lesson $lesson)
    {
        // 1. Kiểm tra quyền
        $this->authorize('update', $lesson->chapter->course);

        // 2. Xóa
        $lesson->delete();

        return back()->with('success', 'Đã xóa bài học!');
    }
    public function show(Course $course)
    {
        // 1. Đếm số lượng Like, Dislike, Comment (Laravel hỗ trợ sẵn withCount)
        $course->loadCount(['likes', 'dislikes', 'comments']);

        // 2. Tính Tổng Lượt Xem (Views)
        // Logic: Cộng tổng số người xem của tất cả các bài học trong khóa này
        // Ta load relationship 'lessons' kèm theo count 'viewers' để tối ưu query
        $course->load(['lessons' => function ($query) {
            $query->withCount('viewers');
        }]);

        // Cộng dồn viewers_count của từng bài học
        $totalViews = $course->lessons->sum('viewers_count');

        // 3. Lấy danh sách Comment chi tiết (Kèm thông tin User để hiện tên/avatar)
        $comments = $course->comments()->with('user')->latest()->get();

        // 4. Trả về View
        return view('teacher.courses.show', compact('course', 'comments', 'totalViews'));
    }
}
