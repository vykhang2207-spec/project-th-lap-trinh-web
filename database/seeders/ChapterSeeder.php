<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;

class ChapterSeeder extends Seeder
{
    public function run(): void
    {
        // Lay tat ca khoa hoc
        Course::all()->each(function (Course $course) {
            // Tao 3-5 chuong cho moi khoa hoc
            Chapter::factory()
                ->count(rand(3, 5))
                ->sequence(fn($sequence) => [
                    'order_index' => $sequence->index + 1,
                    'title' => 'Chương ' . ($sequence->index + 1) . ': Kiến thức nền tảng',
                ])
                ->create([
                    'course_id' => $course->id,
                ]);
        });
    }
}
