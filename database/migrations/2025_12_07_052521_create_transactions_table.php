<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Nguoi mua
            $table->foreignId('course_id')->constrained(); // Khoa hoc

            // Thong tin thanh toan
            $table->decimal('total_amount', 15, 0);      // Tong tien
            $table->decimal('tax_amount', 15, 0);        // Thue
            $table->decimal('admin_fee', 15, 0);         // Phi admin
            $table->decimal('teacher_earning', 15, 0);   // Tien giao vien nhan
            $table->string('payment_method')->default('system');
            $table->string('status')->default('pending'); // Trang thai
            $table->string('transaction_id')->nullable(); // Ma giao dich
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
