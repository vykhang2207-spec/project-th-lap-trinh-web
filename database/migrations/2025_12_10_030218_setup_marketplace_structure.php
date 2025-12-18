<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Them thong tin ngan hang cho user
        Schema::table('users', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('password');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');

            // Xoa cot cu neu co
            if (Schema::hasColumn('users', 'account_balance')) {
                $table->dropColumn('account_balance');
            }
        });

        // 2. Them cot trang thai tra luong
        Schema::table('transactions', function (Blueprint $table) {
            // pending: chua tra, completed: da tra
            $table->string('payout_status')->default('pending')->after('status');

            if (Schema::hasColumn('transactions', 'type')) {
                $table->dropColumn('type');
            }
        });

        // 3. Tao bang lich su tra luong
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users'); // Giao vien duoc tra
            $table->decimal('amount', 15, 0); // So tien
            $table->string('batch_id')->nullable(); // Ma dot tra
            $table->string('status')->default('completed');
            $table->timestamp('paid_at')->useCurrent(); // Ngay tra
            $table->text('note')->nullable(); // Ghi chu
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
