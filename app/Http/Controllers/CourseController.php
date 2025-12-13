<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseReaction; // Nhớ import model này
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\IntelephenseHelpers as AuthHelper;

class CourseController extends Controller
{
    /**
     * HIỂN THỊ CHI TIẾT KHÓA HỌC (Public View)
     * Route: GET /course/{course}
     */
    public function show($id)
    {
        // 1. Tìm khóa học và lấy kèm dữ liệu (Eager Loading)
        // - teacher: Để hiện tên giảng viên
        // - chapters.lessons: Để hiện danh sách bài học
        // - withCount: Đếm số lượng học viên (enrollments), like, dislike
        $course = Course::with(['teacher', 'chapters.lessons'])
            ->withCount(['enrollments', 'likes', 'dislikes'])
            ->findOrFail($id);

        // 2. LOGIC KIỂM TRA QUYỀN XEM (Code cũ của bạn)
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
            abort(404, 'Khóa học này chưa được công khai hoặc đang chờ duyệt.');
        }

        // 3. LẤY DANH SÁCH BÌNH LUẬN
        // Lấy kèm thông tin user để hiện Avatar/Tên người bình luận
        // Phân trang 5 bình luận mỗi lần tải
        $comments = $course->comments()->with('user')->latest()->paginate(5);

        // 4. KIỂM TRA TRẠNG THÁI LIKE/DISLIKE CỦA USER HIỆN TẠI
        // Để tô màu nút Like/Dislike nếu họ đã bấm trước đó
        $userReaction = null;
        if (Auth::check()) {
            $userReaction = $course->isReactedBy(Auth::user());
        }

        // 5. Trả về view
        return view('courses.show', compact('course', 'comments', 'userReaction'));
    }

    /**
     * XỬ LÝ LIKE / DISLIKE
     * Route: POST /course/{course}/reaction
     */
    public function reaction(Request $request, Course $course)
    {
        $request->validate(['type' => 'required|in:like,dislike']);
        $user = Auth::user();
        $type = $request->type;

        $reaction = \App\Models\CourseReaction::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        // --- LOGIC XỬ LÝ (GIỮ NGUYÊN) ---
        if ($reaction) {
            if ($reaction->type === $type) {
                $reaction->delete(); // Unlike
                $currentType = null; // Không còn trạng thái
            } else {
                $reaction->update(['type' => $type]); // Đổi trạng thái
                $currentType = $type;
            }
        } else {
            \App\Models\CourseReaction::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'type' => $type
            ]);
            $currentType = $type;
        }

        // --- 👇 ĐOẠN MỚI QUAN TRỌNG: TRẢ VỀ JSON CHO AJAX ---
        if ($request->wantsJson()) {
            // Đếm lại số lượng mới nhất
            $likesCount = $course->reactions()->where('type', 'like')->count();
            $dislikesCount = $course->reactions()->where('type', 'dislike')->count();

            return response()->json([
                'status' => 'success',
                'likes_count' => $likesCount,
                'dislikes_count' => $dislikesCount,
                'user_reaction' => $currentType, // Trả về 'like', 'dislike' hoặc null
            ]);
        }

        return back();
    }

    /**
     * XỬ LÝ GỬI BÌNH LUẬN
     * Route: POST /course/{course}/comment
     */
    public function storeComment(Request $request, Course $course)
    {
        // Validate nội dung bình luận
        $request->validate([
            'content' => 'required|string|max:1000', // Giới hạn 1000 ký tự
        ], [
            'content.required' => 'Nội dung bình luận không được để trống.',
            'content.max' => 'Bình luận không được quá 1000 ký tự.',
        ]);

        // Tạo bình luận mới thông qua relationship
        $course->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return back()->with('status', 'Bình luận của bạn đã được đăng!');
    }
    public function deleteComment($id)
    {
        $comment = \App\Models\Comment::findOrFail($id);

        /** @var \App\Models\User $user */
        if (auth()->user()->role !== 'admin' && auth()->id() !== $comment->user_id) {
            abort(403, 'Bạn không có quyền xóa bình luận này.');
        }

        $comment->delete();

        return back()->with('status', 'Đã xóa bình luận thành công.');
    }
}
