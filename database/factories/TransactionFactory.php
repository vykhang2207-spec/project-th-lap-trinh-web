<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        // Tinh toan tien va phi
        $totalAmount = $this->faker->numberBetween(100000, 2000000);
        $tax = $totalAmount * 0.10;
        $adminFee = $totalAmount * 0.20;
        $teacherEarning = $totalAmount - $tax - $adminFee;

        return [
            'payment_method' => 'momo',
            'status' => 'success',
            'payout_status' => 'pending',
            'transaction_id' => 'PAY_' . $this->faker->unique()->numerify('##########'),
            'total_amount' => $totalAmount,
            'tax_amount' => $tax,
            'admin_fee' => $adminFee,
            'teacher_earning' => $teacherEarning,
        ];
    }
}
