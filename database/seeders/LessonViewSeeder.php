<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // 👇 Quan trọng
use App\Models\Lesson;
use App\Models\User;

class LessonViewSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lấy danh sách Users (Học viên) và Lessons
        $students = User::where('role', 'student')->get();
        $lessons = Lesson::all();

        if ($students->isEmpty() || $lessons->isEmpty()) {
            return;
        }

        // 2. Tạo 200 lượt xem ngẫu nhiên
        for ($i = 0; $i < 200; $i++) {
            $student = $students->random();
            $lesson = $lessons->random();

            // Sử dụng updateOrInsert để tránh lỗi trùng lặp (Duplicate Entry)
            // Cú pháp: (Điều kiện tìm), (Dữ liệu update/insert)
            DB::table('lesson_views')->updateOrInsert(
                [
                    'user_id' => $student->id,
                    'lesson_id' => $lesson->id,
                ],
                [
                    'last_viewed_at' => fake()->dateTimeBetween('-1 month', 'now')
                ]
            );
        }
    }
}
