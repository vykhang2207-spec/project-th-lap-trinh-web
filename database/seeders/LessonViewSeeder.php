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
        // Lay danh sach hoc vien va bai hoc
        $students = User::where('role', 'student')->get();
        $lessons = Lesson::all();

        if ($students->isEmpty() || $lessons->isEmpty()) {
            return;
        }

        // Tao 200 luot xem ngau nhien
        for ($i = 0; $i < 200; $i++) {
            $student = $students->random();
            $lesson = $lessons->random();

            // Cap nhat hoac them moi de tranh trung lap
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
