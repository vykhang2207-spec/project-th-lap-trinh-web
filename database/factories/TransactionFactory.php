<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $totalAmount = $this->faker->numberBetween(100000, 2000000);
        $tax = $totalAmount * 0.10;
        $adminFee = $totalAmount * 0.20;
        $teacherEarning = $totalAmount - $tax - $adminFee;

        return [
            // Không cần cột 'type' nữa nếu bạn đã xóa nó trong migration mới
            // Nếu chưa xóa cột type trong DB cũ thì cứ để default là 'payment'
            // 'type' => 'payment', 

            'payment_method' => 'momo',
            'status' => 'success',
            'payout_status' => 'pending', // Mặc định chưa trả lương
            'transaction_id' => 'PAY_' . $this->faker->unique()->numerify('##########'),

            'total_amount' => $totalAmount,
            'tax_amount' => $tax,
            'admin_fee' => $adminFee,
            'teacher_earning' => $teacherEarning,
        ];
    }
}
