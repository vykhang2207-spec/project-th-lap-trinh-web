<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 👇 QUAN TRỌNG: Phải có dòng này thì Laravel mới hiểu đây là Migration
return new class extends Migration
{
    public function up(): void
    {
        // 1. Thêm thông tin ngân hàng cho User (để Admin biết đường chuyển khoản)
        Schema::table('users', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('password'); // VCB, MB...
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
            // Xóa cột account_balance cũ nếu có
            if (Schema::hasColumn('users', 'account_balance')) {
                $table->dropColumn('account_balance');
            }
        });

        // 2. Thêm trạng thái trả lương vào Transaction
        Schema::table('transactions', function (Blueprint $table) {
            // payout_status: 'pending' (Chưa trả cho GV), 'completed' (Đã trả)
            $table->string('payout_status')->default('pending')->after('status');

            // Xóa các cột thừa của logic cũ nếu có
            if (Schema::hasColumn('transactions', 'type')) {
                $table->dropColumn('type');
            }
        });

        // 3. Tạo bảng Payouts (Lịch sử trả lương tháng)
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users'); // Trả cho ai
            $table->decimal('amount', 15, 0); // Tổng số tiền trả
            $table->string('batch_id')->nullable(); // Mã đợt trả (VD: PAY_OCT_2023)
            $table->string('status')->default('completed'); // completed
            $table->timestamp('paid_at')->useCurrent(); // Ngày trả
            $table->text('note')->nullable(); // Ghi chú của Admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('payout_status');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_number', 'bank_account_name']);
        });
    }
};
