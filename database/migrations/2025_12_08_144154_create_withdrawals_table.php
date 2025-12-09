<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Giáo viên rút tiền
            $table->decimal('amount', 15, 0); // Số tiền rút

            // Thông tin ngân hàng nhận tiền
            $table->string('bank_name'); // VD: Vietcombank
            $table->string('bank_account_number'); // VD: 0123456789
            $table->string('bank_account_name'); // VD: NGUYEN VAN A

            // Trạng thái: pending (chờ), approved (duyệt), rejected (từ chối)
            $table->string('status')->default('pending');

            $table->text('admin_note')->nullable(); // Ghi chú của Admin (nếu có)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
