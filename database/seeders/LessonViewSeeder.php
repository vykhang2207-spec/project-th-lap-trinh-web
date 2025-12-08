<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Lesson;
use App\Models\User;

class LessonViewSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lấy danh sách Users và Lessons đang có
        $users = User::all();
        $lessons = Lesson::all();

        // Kiểm tra nếu chưa có data thì thôi
        if ($users->isEmpty() || $lessons->isEmpty()) {
            return;
        }

        // 2. Duyệt qua từng bài học
        foreach ($lessons as $lesson) {
            // Random: Mỗi bài học sẽ có từ 0 đến 5 người xem ngẫu nhiên
            $randomViewers = $users->random(min($users->count(), rand(0, 5)));

            foreach ($randomViewers as $user) {
                // Kiểm tra xem đã view chưa bằng DB query trực tiếp (vì không có Model LessonView)
                $exists = DB::table('lesson_views')
                    ->where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->exists();

                if (!$exists) {
                    // 3. Gắn User vào Lesson
                    $lesson->viewers()->attach($user->id, [
                        'last_viewed_at' => fake()->dateTimeBetween('-1 month', 'now')
                    ]);
                }
            }
        }
    }
}
