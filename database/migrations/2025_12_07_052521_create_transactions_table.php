<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_transactions_table.php

    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Người mua
            $table->foreignId('course_id')->constrained(); // Khóa học

            // CÁC CỘT TIỀN BẠC
            $table->decimal('total_amount', 15, 0);      // 1. Tổng tiền khách trả (Ví dụ: 400k)
            $table->decimal('tax_amount', 15, 0);        // 2. Thuế (10% = 40k)
            $table->decimal('admin_fee', 15, 0);         // 3. Phí sàn Admin ăn (Ví dụ 20% = 80k)
            $table->decimal('teacher_earning', 15, 0);   // 4. Tiền thực nhận của GV (280k)
            $table->string('payment_method')->default('system'); // Ví dụ: momo, vnpay, banking
            $table->string('status')->default('pending'); // pending, success, failed
            $table->string('transaction_id')->nullable(); // Mã giao dịch ngân hàng (nếu có)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
