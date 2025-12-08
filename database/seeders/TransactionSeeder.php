<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Course;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lấy danh sách Học viên và Khóa học có sẵn
        // (Giả sử học viên là role 'student', hoặc lấy tất cả user cũng được)
        $students = User::all();
        $courses = Course::all();

        // Kiểm tra nếu chưa có dữ liệu thì dừng
        if ($students->isEmpty() || $courses->isEmpty()) {
            return;
        }

        // 2. Tạo 50 giao dịch ngẫu nhiên
        foreach (range(1, 50) as $index) {

            $student = $students->random();
            $course = $courses->random();

            // LOGIC TÍNH TIỀN (Quan trọng)
            // Lấy giá gốc từ khóa học (hoặc random nếu khóa học free)
            $price = $course->price > 0 ? $course->price : rand(100000, 2000000);

            $tax = $price * 0.10; // Thuế 10%
            $adminFee = $price * 0.20; // Phí sàn 20%
            $teacherEarning = $price - $tax - $adminFee; // GV nhận phần còn lại

            // 3. Tạo giao dịch (Dùng create trực tiếp để tránh lỗi Factory cũ)
            Transaction::create([
                'user_id' => $student->id,
                'course_id' => $course->id,

                // Điền đủ 4 cột tiền mới
                'total_amount' => $price,
                'tax_amount' => $tax,
                'admin_fee' => $adminFee,
                'teacher_earning' => $teacherEarning,

                // Các cột khác
                'payment_method' => 'momo',
                'status' => 'success',
                'transaction_id' => 'MOMO' . rand(10000000, 99999999),
                'created_at' => fake()->dateTimeBetween('-6 months', 'now'), // Random ngày mua trong 6 tháng qua
            ]);
        }
    }
}
