<?php

use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// Controllers chung
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MomoSimulationController;
// Controllers Admin
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminCourseController;
// Controllers Teacher
use App\Http\Controllers\TeacherRevenueController;
use App\Http\Controllers\TeacherWithdrawalController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;

Route::get('/hack-progress', function () {
    $user = Auth::user();
    if (!$user) return "Bạn phải đăng nhập trước đã!";

    // 1. Lấy danh sách các khóa học mà User này đã mua
    $enrolledCourseIds = Enrollment::where('user_id', $user->id)->pluck('course_id');

    if ($enrolledCourseIds->isEmpty()) {
        return "Bạn chưa mua khóa nào cả. Hãy chạy Seeder lại hoặc mua đại 1 khóa đi.";
    }

    $count = 0;

    // 2. Duyệt qua từng khóa học đã mua
    foreach ($enrolledCourseIds as $courseId) {
        // Lấy tất cả bài học của khóa đó
        $lessons = Lesson::whereHas('chapter', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        })->get();

        if ($lessons->isEmpty()) continue;

        // 3. Random lấy khoảng 30% - 70% số bài để đánh dấu là "Đã học"
        $lessonsToLearn = $lessons->random(rand((int)($lessons->count() * 0.3), (int)($lessons->count() * 0.7)));

        foreach ($lessonsToLearn as $lesson) {
            // Nếu chưa có Model LessonView thì dùng DB::table cũng được, nhưng ở đây mình giả định bạn đã fix hoặc dùng DB
            \Illuminate\Support\Facades\DB::table('lesson_views')->updateOrInsert(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['last_viewed_at' => now()]
            );
            $count++;
        }
    }

    return redirect()->route('dashboard')->with('status', "Đã hack xong! Đánh dấu hoàn thành $count bài học. Kiểm tra thanh tiến độ đi!");
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Public Routes (Không cần đăng nhập)
|--------------------------------------------------------------------------
*/
// Route API cho Live Search
Route::get('/search-suggestions', [HomeController::class, 'searchSuggestions'])->name('search.suggestions');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/course/{course}', [CourseController::class, 'show'])->name('course.show');

// Route giả lập Cổng thanh toán MoMo
Route::get('/momo-gateway-simulation', [MomoSimulationController::class, 'show'])->name('momo.simulation');
Route::post('/momo-gateway-simulation/success', [MomoSimulationController::class, 'success'])->name('momo.simulation.success');
Route::post('/momo-gateway-simulation/cancel', [MomoSimulationController::class, 'cancel'])->name('momo.simulation.cancel');

// Route xem hồ sơ giảng viên (Public)
Route::get('/teacher-profile/{id}', [HomeController::class, 'teacherProfile'])->name('teacher.profile');
/*
|--------------------------------------------------------------------------
| Authenticated Routes (Cần đăng nhập)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Thêm vào nhóm middleware auth
    Route::delete('/comment/{id}', [App\Http\Controllers\CourseController::class, 'deleteComment'])->name('comment.destroy');
    // Route ajax đánh dấu hoàn thành bài học
    Route::post('/lesson/{id}/complete', [App\Http\Controllers\LessonController::class, 'complete'])
        ->name('lesson.complete');
    // 👇 1. ROUTE DASHBOARD (Đóng ngoặc kết thúc ở đây luôn)
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Teacher -> Vào trang quản lý khóa học
        if ($user->role === 'teacher') {
            return redirect()->route('teacher.courses.index');
        }

        // 2. Admin -> Vào trang quản lý giao dịch
        if ($user->role === 'admin') {
            return redirect()->route('admin.transactions.index');
        }

        // 3. Student -> Ở lại Dashboard học viên
        $myCourses = $user->enrollments()->with('course.teacher')->latest()->get();
        return view('dashboard', compact('myCourses'));
    })->name('dashboard');
    // 👆 Kết thúc Route Dashboard ở đây.


    // 👇 2. CÁC ROUTE TƯƠNG TÁC (Đưa ra ngoài, nằm ngang hàng với dashboard)
    Route::post('/course/{course}/reaction', [CourseController::class, 'reaction'])->name('course.reaction');
    Route::post('/course/{course}/comment', [CourseController::class, 'storeComment'])->name('course.comment');


    /* =========================================
     * STUDENT ROUTES (Học viên)
     * ========================================= */
    // Lesson Learning
    Route::get('/learning/{course}/{lesson}', [LessonController::class, 'show'])->name('lesson.show');

    // Payment Flow
    Route::get('/course/{course}/checkout', [PaymentController::class, 'create'])->name('payment.checkout');
    Route::post('/course/{course}/pay', [PaymentController::class, 'store'])->name('payment.process');
    Route::get('/course/{course}/payment-callback', [PaymentController::class, 'callback'])->name('payment.callback');


    /* =========================================
     * TEACHER ROUTES (Giáo viên)
     * ========================================= */
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        // Quản lý Khóa học
        Route::get('/courses', [TeacherCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [TeacherCourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [TeacherCourseController::class, 'store'])->name('courses.store');

        Route::get('/courses/{course}', [TeacherCourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/edit', [TeacherCourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [TeacherCourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [TeacherCourseController::class, 'destroy'])->name('courses.destroy');

        // Quản lý Nội dung
        Route::get('/courses/{course}/content', [TeacherCourseController::class, 'contentIndex'])->name('courses.content.index');
        Route::post('/courses/{course}/chapters', [TeacherCourseController::class, 'storeChapter'])->name('courses.chapters.store');
        Route::post('/chapters/{chapter}/lessons', [TeacherCourseController::class, 'storeLesson'])->name('chapters.lessons.store');
        Route::put('/lessons/{lesson}', [TeacherCourseController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [TeacherCourseController::class, 'destroyLesson'])->name('lessons.destroy');

        // Doanh thu
        Route::get('/revenue', [TeacherRevenueController::class, 'index'])->name('revenue.index');
        Route::post('/withdraw', [TeacherWithdrawalController::class, 'store'])->name('withdraw.store');
    });


    /* =========================================
     * ADMIN ROUTES (Quản trị viên)
     * ========================================= */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
        Route::patch('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('courses.approve');
    });


    /* =========================================
     * PROFILE ROUTES
     * ========================================= */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
