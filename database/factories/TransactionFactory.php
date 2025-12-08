<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        // 1. Giả lập tổng tiền khách trả (Ví dụ từ 100k đến 2 triệu)
        $totalAmount = $this->faker->numberBetween(100000, 2000000);

        // 2. Tính toán các khoản chia (Giống logic thực tế)
        $tax = $totalAmount * 0.10; // Thuế 10%
        $adminFee = $totalAmount * 0.20; // Phí sàn 20%
        $teacherEarning = $totalAmount - $tax - $adminFee; // Còn lại của GV

        return [
            // Xóa dòng 'amount' cũ đi, thay bằng 4 dòng này:
            'total_amount' => $totalAmount,
            'tax_amount' => $tax,
            'admin_fee' => $adminFee,
            'teacher_earning' => $teacherEarning,

            // Các cột khác giữ nguyên (nếu có)
            'status' => 'success',
            'payment_method' => 'momo', // Ví dụ
            'transaction_id' => 'MOMO' . $this->faker->randomNumber(8),
            // user_id và course_id thường được gán lúc gọi Seeder, hoặc bạn để factory tự tạo cũng được
        ];
    }
}
