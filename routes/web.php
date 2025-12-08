<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController; // Controller hiển thị cho học viên (Public)
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MomoSimulationController;
use App\Http\Controllers\TeacherRevenueController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminCourseController;
// 👇 Import Controller của Giáo viên
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes (Không cần đăng nhập)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/course/{course}', [CourseController::class, 'show'])->name('course.show');

// Route giả lập Cổng thanh toán MoMo
Route::get('/momo-gateway-simulation', [MomoSimulationController::class, 'show'])->name('momo.simulation');
Route::post('/momo-gateway-simulation/success', [MomoSimulationController::class, 'success'])->name('momo.simulation.success');
Route::post('/momo-gateway-simulation/cancel', [MomoSimulationController::class, 'cancel'])->name('momo.simulation.cancel');


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Cần đăng nhập)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // 👇 LOGIC ĐIỀU HƯỚNG DASHBOARD
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Nếu là Teacher -> Vào trang quản lý khóa học
        if ($user->role === 'teacher') {
            return redirect()->route('teacher.courses.index');
        }

        // 2. Nếu là Admin -> Vào trang quản lý giao dịch
        if ($user->role === 'admin') {
            return redirect()->route('admin.transactions.index');
        }

        // 3. Nếu là Student -> Ở lại Dashboard học viên
        $myCourses = $user->enrollments()->with('course.teacher')->latest()->get();
        return view('dashboard', compact('myCourses'));
    })->middleware(['verified'])->name('dashboard');


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
     * TEACHER ROUTES (Dành riêng cho Giáo viên)
     * ========================================= */
    // Middleware: Chỉ cho phép role 'teacher' (hoặc 'admin' nếu muốn xem)
    Route::middleware('role:teacher|admin')->prefix('teacher')->name('teacher.')->group(function () {

        // 1. Quản lý Khóa học (CRUD)
        Route::get('/courses', [TeacherCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [TeacherCourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [TeacherCourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}/edit', [TeacherCourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [TeacherCourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [TeacherCourseController::class, 'destroy'])->name('courses.destroy');

        // 2. Trang xem chi tiết (Overview)
        Route::get('/courses/{course}', [TeacherCourseController::class, 'show'])->name('courses.show');

        // 3. Quản lý Nội dung (Chương/Bài học)
        Route::get('/courses/{course}/content', [TeacherCourseController::class, 'contentIndex'])->name('courses.content.index');
        Route::post('/courses/{course}/chapters', [TeacherCourseController::class, 'storeChapter'])->name('courses.chapters.store');
        Route::post('/chapters/{chapter}/lessons', [TeacherCourseController::class, 'storeLesson'])->name('chapters.lessons.store');
        Route::put('/lessons/{lesson}', [TeacherCourseController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [TeacherCourseController::class, 'destroyLesson'])->name('lessons.destroy');

        // 4. Xem Doanh thu (Của riêng giáo viên)
        Route::get('/revenue', [TeacherRevenueController::class, 'index'])->name('revenue.index');
    });


    /* =========================================
     * ADMIN ROUTES (Dành riêng cho Admin)
     * ========================================= */
    // Middleware: Chỉ cho phép role 'admin'
    // Đã sửa: Xóa bỏ việc lồng Route group dư thừa ở đây
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // 1. Quản lý Giao dịch toàn hệ thống
        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');

        // 2. Quản lý Khóa học (Duyệt bài)
        Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
        // 👇 THÊM DÒNG NÀY: Route để xử lý duyệt
        Route::patch('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('courses.approve');
    });


    /* =========================================
     * PROFILE ROUTES (Chung cho tất cả)
     * ========================================= */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
