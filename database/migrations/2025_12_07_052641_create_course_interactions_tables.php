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
        // 1. SỬA TÊN BẢNG Ở ĐÂY: đổi 'course_interactions' thành 'lesson_views'
        Schema::create('lesson_views', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->timestamp('last_viewed_at')->useCurrent();
            $table->primary(['user_id', 'lesson_id']);
        });

        // 2. Bảng Bình luận (Giữ nguyên)
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });

        // 3. Bảng Cảm xúc (Giữ nguyên)
        Schema::create('course_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->enum('type', ['like', 'dislike']);
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa theo thứ tự ngược lại để tránh lỗi khóa ngoại
        Schema::dropIfExists('course_reactions');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('lesson_views');
    }
};
