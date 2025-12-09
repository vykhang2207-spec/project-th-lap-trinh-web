<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Enrollment;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $successfulTransactions = Transaction::where('status', 'success')->get();

        foreach ($successfulTransactions as $transaction) {
            // firstOrCreate: Tìm xem có chưa, chưa có thì tạo mới
            Enrollment::firstOrCreate([
                'user_id' => $transaction->user_id,
                'course_id' => $transaction->course_id,
            ], [
                'created_at' => $transaction->created_at,
            ]);
        }
    }
}
