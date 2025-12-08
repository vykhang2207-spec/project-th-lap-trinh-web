<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Enrollment;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả các giao dịch thành công để tạo bản ghi cấp quyền
        $successfulTransactions = Transaction::where('status', 'success')->get();

        foreach ($successfulTransactions as $transaction) {
            // Kiểm tra tránh trùng lặp trước khi tạo
            if (!Enrollment::where('user_id', $transaction->user_id)
                ->where('course_id', $transaction->course_id)
                ->exists()) {

                Enrollment::factory()->create([
                    'user_id' => $transaction->user_id,
                    'course_id' => $transaction->course_id,
                ]);
            }
        }
    }
}
