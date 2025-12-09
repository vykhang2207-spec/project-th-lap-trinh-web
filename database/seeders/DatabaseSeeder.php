<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Comment;
use App\Models\CourseReaction;

// Import các Seeder
use Database\Seeders\UserSeeder;
use Database\Seeders\CourseSeeder;
use Database\Seeders\ChapterSeeder;
use Database\Seeders\LessonSeeder;
use Database\Seeders\TransactionSeeder;
use Database\Seeders\EnrollmentSeeder;
use Database\Seeders\LessonViewSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. GỌI CÁC SEEDER CẤU TRÚC (Theo thứ tự)
        $this->call([
            UserSeeder::class,
            CourseSeeder::class,
            ChapterSeeder::class,
            LessonSeeder::class,
            TransactionSeeder::class,
            EnrollmentSeeder::class,
            LessonViewSeeder::class, // Đã fix lỗi ở trên
        ]);

        // 2. LOGIC TẠO COMMENT VÀ REACTION (Giữ nguyên tại đây)
        $this->createCommentsAndReactions();

        $this->command->info('✅ Tất cả dữ liệu mẫu đã được tạo thành công!');
    }

    /**
     * Hàm riêng để xử lý logic Comment và Like/Dislike
     */
    private function createCommentsAndReactions(): void
    {
        $users = User::where('role', 'student')->get(); // Chỉ lấy học viên để comment
        $courses = Course::all();

        if ($users->isEmpty() || $courses->isEmpty()) {
            return;
        }

        $this->command->info('Generating Comments and Reactions...');

        foreach ($courses as $course) {
            // A. TẠO COMMENT (Ngẫu nhiên 0-3 comment mỗi khóa)
            $randomCommenters = $users->random(min($users->count(), rand(0, 3)));

            foreach ($randomCommenters as $user) {
                Comment::factory()->create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ]);
            }

            // B. TẠO REACTION (LIKE/DISLIKE)
            $randomReactors = $users->random(min($users->count(), rand(0, 5)));

            foreach ($randomReactors as $user) {
                // Kiểm tra xem user đã like chưa
                $hasReacted = CourseReaction::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->exists();

                if (!$hasReacted) {
                    CourseReaction::factory()->create([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                    ]);
                }
            }
        }
    }
}
