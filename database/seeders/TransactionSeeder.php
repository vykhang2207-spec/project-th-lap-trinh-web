<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lấy danh sách Học viên và Khóa học
        $students = User::where('role', 'student')->get();
        $courses = Course::all();

        if ($students->isEmpty() || $courses->isEmpty()) {
            return;
        }

        foreach ($students as $student) {

            // Mỗi học viên mua ngẫu nhiên 1-4 khóa học
            $randomCourses = $courses->random(min($courses->count(), rand(1, 4)));

            foreach ($randomCourses as $course) {
                // Kiểm tra trùng lặp Enrollment
                $exists = Enrollment::where('user_id', $student->id)
                    ->where('course_id', $course->id)
                    ->exists();

                if (!$exists) {
                    // Tính toán chia tiền
                    $price = $course->price;
                    $tax = $price * 0.10;
                    $adminFee = $price * 0.20;
                    $teacherEarning = $price - $tax - $adminFee;

                    // Tạo Transaction (Chỉ có loại PAYMENT, không có DEPOSIT)
                    $transaction = Transaction::factory()->create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,

                        'total_amount' => $price,
                        'tax_amount' => $tax,
                        'admin_fee' => $adminFee,
                        'teacher_earning' => $teacherEarning,

                        'status' => 'success',
                        'payout_status' => 'pending', // Mặc định là chưa trả lương cho GV

                        'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
                    ]);

                    // ❌ KHÔNG CÒN TRỪ VÍ USER NỮA
                    // $student->decrement('account_balance', $price); -> XÓA

                    // ❌ KHÔNG CỘNG VÍ GV NGAY LẬP TỨC NỮA (Chờ Admin Payout)
                    // $course->teacher->increment('account_balance', $teacherEarning); -> XÓA

                    // Cấp quyền học ngay lập tức
                    Enrollment::firstOrCreate([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                    ], [
                        'created_at' => $transaction->created_at
                    ]);
                }
            }
        }
    }
}
