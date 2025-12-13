<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Models (Cho Route Closure hack-progress)
use App\Models\Enrollment;
use App\Models\Lesson;

// Controllers Chung
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MomoSimulationController;
use App\Http\Controllers\ProfileController;

// Controllers Admin
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminCourseController;
use App\Http\Controllers\AdminPayoutController; // MỚI: Trả lương GV

// Controllers Teacher
use App\Http\Controllers\TeacherRevenueController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search-suggestions', [HomeController::class, 'searchSuggestions'])->name('search.suggestions');
Route::get('/course/{course}', [CourseController::class, 'show'])->name('course.show');
Route::get('/teacher-profile/{id}', [HomeController::class, 'teacherProfile'])->name('teacher.profile');

// MoMo Simulation
Route::get('/momo-gateway-simulation', [MomoSimulationController::class, 'show'])->name('momo.simulation');
Route::post('/momo-gateway-simulation/success', [MomoSimulationController::class, 'success'])->name('momo.simulation.success');
Route::post('/momo-gateway-simulation/cancel', [MomoSimulationController::class, 'cancel'])->name('momo.simulation.cancel');

// Route Hack Progress (Giữ lại để test)
Route::get('/hack-progress', function () {
    $user = Auth::user();
    if (!$user) return "Login required!";

    // 1. Lấy danh sách các khóa học mà User này đã mua
    $enrolledCourseIds = Enrollment::where('user_id', $user->id)->pluck('course_id');

    if ($enrolledCourseIds->isEmpty()) {
        return "Bạn chưa mua khóa nào cả.";
    }

    $count = 0;
    foreach ($enrolledCourseIds as $courseId) {
        $lessons = Lesson::whereHas('chapter', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        })->get();

        if ($lessons->isEmpty()) continue;

        $lessonsToLearn = $lessons->random(min($lessons->count(), rand((int)($lessons->count() * 0.3), (int)($lessons->count() * 0.7))));

        foreach ($lessonsToLearn as $lesson) {
            \Illuminate\Support\Facades\DB::table('lesson_views')->updateOrInsert(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['last_viewed_at' => now()]
            );
            $count++;
        }
    }
    return redirect()->route('dashboard')->with('status', "Đã hack xong! Đánh dấu hoàn thành $count bài học.");
})->middleware('auth');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. DASHBOARD & COMMON
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->role === 'teacher') return redirect()->route('teacher.courses.index');
        if ($user->role === 'admin') return redirect()->route('admin.transactions.index');

        $myCourses = $user->enrollments()->with('course.teacher')->latest()->get();
        return view('dashboard', compact('myCourses'));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. INTERACTION (Comment, Like)
    Route::post('/course/{course}/reaction', [CourseController::class, 'reaction'])->name('course.reaction');
    Route::post('/course/{course}/comment', [CourseController::class, 'storeComment'])->name('course.comment');
    Route::delete('/comment/{id}', [CourseController::class, 'deleteComment'])->name('comment.destroy');

    /* =========================================
     * STUDENT ROUTES
     * ========================================= */
    Route::get('/learning/{course}/{lesson}', [LessonController::class, 'show'])->name('lesson.show');
    Route::post('/lesson/{id}/complete', [LessonController::class, 'complete'])->name('lesson.complete');

    // Payment (Direct MoMo)
    Route::get('/course/{course}/checkout', [PaymentController::class, 'create'])->name('payment.checkout');
    Route::post('/course/{course}/pay', [PaymentController::class, 'store'])->name('payment.process');
    Route::get('/course/{course}/payment-callback', [PaymentController::class, 'callback'])->name('payment.callback');

    /* =========================================
     * TEACHER ROUTES
     * ========================================= */
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        // Quản lý Khóa học & Nội dung
        Route::resource('courses', TeacherCourseController::class);

        // Quản lý Nội dung chi tiết
        Route::get('/courses/{course}/content', [TeacherCourseController::class, 'contentIndex'])->name('courses.content.index');
        Route::post('/courses/{course}/chapters', [TeacherCourseController::class, 'storeChapter'])->name('courses.chapters.store');
        Route::post('/chapters/{chapter}/lessons', [TeacherCourseController::class, 'storeLesson'])->name('chapters.lessons.store');
        Route::put('/lessons/{lesson}', [TeacherCourseController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [TeacherCourseController::class, 'destroyLesson'])->name('lessons.destroy');

        // Xem Doanh thu & Lương (Revenue) - ĐÃ SỬA CHỖ NÀY
        Route::get('/revenue', [TeacherRevenueController::class, 'index'])->name('revenue.index');
        // ❌ ĐÃ XÓA ROUTE RÚT TIỀN (withdraw) VÌ KHÔNG CÒN DÙNG
    });

    /* =========================================
     * ADMIN ROUTES
     * ========================================= */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // 1. Quản lý Giao dịch & Tài chính
        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');

        // 2. Quản lý & Duyệt Khóa học
        Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
        Route::patch('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('courses.approve');

        // 3. QUYẾT TOÁN LƯƠNG (PAYOUTS) - MỚI
        Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
        Route::post('/payouts', [AdminPayoutController::class, 'store'])->name('payouts.store');
    });
});

require __DIR__ . '/auth.php';
