<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    // Hien thi chi tiet khoa hoc
    public function show($id)
    {
        $course = Course::with(['teacher', 'chapters.lessons'])
            ->withCount(['enrollments', 'likes', 'dislikes'])
            ->findOrFail($id);

        // Kiem tra quyen xem
        $canView = false;

        if ($course->is_approved) {
            $canView = true;
        }

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin' || $user->id === $course->teacher_id) {
                $canView = true;
            }
        }

        if (!$canView) {
            abort(404, 'Khóa học này chưa được công khai hoặc đang chờ duyệt.');
        }

        // Lay binh luan
        $comments = $course->comments()->with('user')->latest()->paginate(5);

        // Kiem tra trang thai like cua user
        $userReaction = null;
        if (Auth::check()) {
            $userReaction = $course->isReactedBy(Auth::user());
        }

        return view('courses.show', compact('course', 'comments', 'userReaction'));
    }

    // Xu ly Like/Dislike
    public function reaction(Request $request, Course $course)
    {
        $request->validate(['type' => 'required|in:like,dislike']);
        $user = Auth::user();
        $type = $request->type;

        $reaction = \App\Models\CourseReaction::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($reaction) {
            if ($reaction->type === $type) {
                $reaction->delete(); // Huy like
                $currentType = null;
            } else {
                $reaction->update(['type' => $type]); // Doi trang thai
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

        if ($request->wantsJson()) {
            $likesCount = $course->reactions()->where('type', 'like')->count();
            $dislikesCount = $course->reactions()->where('type', 'dislike')->count();

            return response()->json([
                'status' => 'success',
                'likes_count' => $likesCount,
                'dislikes_count' => $dislikesCount,
                'user_reaction' => $currentType,
            ]);
        }

        return back();
    }

    // Gui binh luan
    public function storeComment(Request $request, Course $course)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ], [
            'content.required' => 'Nội dung bình luận không được để trống.',
            'content.max' => 'Bình luận không được quá 1000 ký tự.',
        ]);

        $course->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return back()->with('status', 'Bình luận của bạn đã được đăng!');
    }

    // Xoa binh luan
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
