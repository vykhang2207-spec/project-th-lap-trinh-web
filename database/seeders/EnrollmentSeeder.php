<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Enrollment;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        // 👇 SỬA DÒNG NÀY: Thêm điều kiện where('type', 'payment')
        $successfulTransactions = Transaction::where('status', 'success')
            ->where('type', 'payment') // Chỉ lấy giao dịch mua khóa học
            ->whereNotNull('course_id') // Chắc ăn là có ID khóa học
            ->get();

        foreach ($successfulTransactions as $transaction) {
            Enrollment::firstOrCreate([
                'user_id' => $transaction->user_id,
                'course_id' => $transaction->course_id,
            ], [
                'created_at' => $transaction->created_at,
            ]);
        }
    }
}
