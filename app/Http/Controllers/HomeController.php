<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    // Trang chu
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Course::with('teacher')
            ->withCount(['enrollments', 'likes', 'dislikes'])
            ->with(['reactions' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->where('is_approved', 1);

        // Xu ly tim kiem
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('teacher', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $courses = $query->latest()->paginate(12);
        $courses->appends(['search' => $request->search]);

        return view('welcome', compact('courses'));
    }

    // Ho so giang vien
    public function teacherProfile($id)
    {
        $userId = Auth::id();
        $teacher = User::where('id', $id)->where('role', 'teacher')->firstOrFail();

        // Lay cac khoa hoc cua giang vien
        $courses = $teacher->courses()
            ->where('is_approved', 1)
            ->withCount(['enrollments', 'likes', 'dislikes'])
            ->with(['reactions' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->latest()
            ->paginate(12);

        return view('teacher.profile', compact('teacher', 'courses'));
    }

    // Goi y tim kiem ajax
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('query');

        if (!$query) {
            return response()->json([]);
        }

        $courses = Course::where('title', 'like', "%{$query}%")
            ->where('is_approved', 1)
            ->with('teacher')
            ->select('id', 'title', 'image_path', 'teacher_id')
            ->take(5)
            ->get();

        return response()->json($courses);
    }
}
