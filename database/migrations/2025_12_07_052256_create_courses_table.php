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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            // Khóa ngoại liên kết với Giảng viên, nếu teacher bị xóa thì khóa học cũng bị xóa
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('image_path')->nullable(); // Đường dẫn ảnh bìa
            $table->decimal('price', 10, 2); // Giá tiền (ví dụ: 10 chữ số, 2 số thập phân)
            // Trạng thái: 0=Chờ duyệt, 1=Đã duyệt, 2=Bị từ chối
            $table->tinyInteger('is_approved')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
