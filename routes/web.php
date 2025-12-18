<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Cac controller dung chung
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MomoSimulationController;
use App\Http\Controllers\ProfileController;

// Cac controller cua Admin
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminCourseController;
use App\Http\Controllers\AdminPayoutController;

// Cac controller cua Giao vien
use App\Http\Controllers\TeacherRevenueController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;


// CAC ROUTE CONG KHAI (KHONG CAN DANG NHAP)

// Trang chu
Route::get('/', [HomeController::class, 'index'])->name('home');

// Goi y tim kiem ajax
Route::get('/search-suggestions', [HomeController::class, 'searchSuggestions'])->name('search.suggestions');

// Xem chi tiet khoa hoc
Route::get('/course/{course}', [CourseController::class, 'show'])->name('course.show');

// Xem ho so giang vien
Route::get('/teacher-profile/{id}', [HomeController::class, 'teacherProfile'])->name('teacher.profile');

// Gia lap cong thanh toan Momo
Route::get('/momo-gateway-simulation', [MomoSimulationController::class, 'show'])->name('momo.simulation');
Route::post('/momo-gateway-simulation/success', [MomoSimulationController::class, 'success'])->name('momo.simulation.success');
Route::post('/momo-gateway-simulation/cancel', [MomoSimulationController::class, 'cancel'])->name('momo.simulation.cancel');


// CAC ROUTE YEU CAU DANG NHAP (AUTH)
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. Dashboard va cac chuc nang chung
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Kiem tra vai tro de chuyen huong ve trang quan ly tuong ung
        if ($user->role === 'teacher') return redirect()->route('teacher.courses.index');
        if ($user->role === 'admin') return redirect()->route('admin.transactions.index');

        // Neu la hoc vien thi lay danh sach khoa hoc cua ho
        $myCourses = $user->enrollments()->with('course.teacher')->latest()->get();
        return view('dashboard', compact('myCourses'));
    })->name('dashboard');

    // Quan ly thong tin ca nhan (Profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. Tuong tac (Like, Binh luan)
    Route::post('/course/{course}/reaction', [CourseController::class, 'reaction'])->name('course.reaction');
    Route::post('/course/{course}/comment', [CourseController::class, 'storeComment'])->name('course.comment');
    Route::delete('/comment/{id}', [CourseController::class, 'deleteComment'])->name('comment.destroy');


    // KHU VUC HOC VIEN (STUDENT)

    // Vao hoc va danh dau hoan thanh bai hoc
    Route::get('/learning/{course}/{lesson}', [LessonController::class, 'show'])->name('lesson.show');
    Route::post('/lesson/{id}/complete', [LessonController::class, 'complete'])->name('lesson.complete');

    // Thanh toan khoa hoc qua Momo
    Route::get('/course/{course}/checkout', [PaymentController::class, 'create'])->name('payment.checkout');
    Route::post('/course/{course}/pay', [PaymentController::class, 'store'])->name('payment.process');
    Route::get('/course/{course}/payment-callback', [PaymentController::class, 'callback'])->name('payment.callback');


    // KHU VUC GIAO VIEN (TEACHER)
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {

        // Quan ly khoa hoc (CRUD co ban)
        Route::resource('courses', TeacherCourseController::class);

        // Quan ly noi dung chi tiet (Chuong va Bai hoc)
        Route::get('/courses/{course}/content', [TeacherCourseController::class, 'contentIndex'])->name('courses.content.index');
        Route::post('/courses/{course}/chapters', [TeacherCourseController::class, 'storeChapter'])->name('courses.chapters.store');
        Route::post('/chapters/{chapter}/lessons', [TeacherCourseController::class, 'storeLesson'])->name('chapters.lessons.store');
        Route::put('/lessons/{lesson}', [TeacherCourseController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [TeacherCourseController::class, 'destroyLesson'])->name('lessons.destroy');

        // Xem bao cao doanh thu va luong
        Route::get('/revenue', [TeacherRevenueController::class, 'index'])->name('revenue.index');
    });


    // KHU VUC ADMIN (QUANTRI VIEN)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // 1. Quan ly giao dich va tai chinh
        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');

        // 2. Quan ly va duyet khoa hoc
        Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
        Route::patch('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('courses.approve');

        // 3. Quyet toan luong cho giao vien
        Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
        Route::post('/payouts', [AdminPayoutController::class, 'store'])->name('payouts.store');
    });
});

require __DIR__ . '/auth.php';
