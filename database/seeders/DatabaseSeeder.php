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
use Database\Seeders\LessonViewSeeder; // <-- Import cái mới tạo

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

            // Gọi Seeder tạo View riêng biệt (Gọn gàng hơn)
            LessonViewSeeder::class,
        ]);

        // 2. LOGIC TẠO COMMENT VÀ REACTION (Giữ lại ở đây cho tiện)
        $this->createCommentsAndReactions();
    }

    /**
     * Hàm riêng để xử lý logic Comment và Like/Dislike
     */
    private function createCommentsAndReactions(): void
    {
        $users = User::all();
        $courses = Course::all();

        // Kiểm tra dữ liệu
        if ($users->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('Skipping Comments/Reactions: No Users or Courses found.');
            return;
        }

        $this->command->info('Generating Comments and Reactions...');

        foreach ($courses as $course) {
            // A. TẠO COMMENT (Ngẫu nhiên 0-3 comment mỗi khóa)
            // Lấy ngẫu nhiên vài user (tối đa 3 người)
            $randomCommenters = $users->random(min($users->count(), rand(0, 3)));

            foreach ($randomCommenters as $user) {
                Comment::factory()->create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ]);
            }

            // B. TẠO REACTION (LIKE/DISLIKE)
            // Lấy ngẫu nhiên vài user (tối đa 5 người)
            $randomReactors = $users->random(min($users->count(), rand(0, 5)));

            foreach ($randomReactors as $user) {
                // Kiểm tra xem user đã like chưa để tránh lỗi trùng lặp (Unique Key)
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

        $this->command->info('Comments and Reactions generated successfully!');
    }
}
