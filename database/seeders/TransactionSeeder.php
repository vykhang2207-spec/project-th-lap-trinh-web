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
        // CHỈ lấy user là 'student'
        $students = User::where('role', 'student')->get();
        $courses = Course::all();

        if ($students->isEmpty() || $courses->isEmpty()) {
            return;
        }

        // Tạo 50 giao dịch
        foreach (range(1, 50) as $index) {
            $student = $students->random();
            $course = $courses->random();

            $price = $course->price > 0 ? $course->price : rand(100000, 2000000);
            $tax = $price * 0.10;
            $adminFee = $price * 0.20;
            $teacherEarning = $price - $tax - $adminFee;

            Transaction::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'total_amount' => $price,
                'tax_amount' => $tax,
                'admin_fee' => $adminFee,
                'teacher_earning' => $teacherEarning,
                'payment_method' => 'momo',
                'status' => 'success',
                'transaction_id' => 'MOMO' . rand(10000000, 99999999),
                'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
            ]);
        }
    }
}
