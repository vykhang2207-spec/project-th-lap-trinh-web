<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Enrollment;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        // Lay cac giao dich thanh cong
        $successfulTransactions = Transaction::where('status', 'success')
            ->where('type', 'payment')
            ->whereNotNull('course_id')
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
