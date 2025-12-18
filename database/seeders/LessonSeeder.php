<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chapter;
use App\Models\Lesson;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        // Lay tat ca chuong
        Chapter::all()->each(function (Chapter $chapter) {
            // Tao 3-8 bai hoc cho moi chuong
            Lesson::factory()
                ->count(rand(3, 8))
                ->sequence(fn($sequence) => [
                    'order_index' => $sequence->index + 1,
                    'title' => 'Bài ' . ($sequence->index + 1) . ': Hướng dẫn chi tiết',
                ])
                ->create([
                    'chapter_id' => $chapter->id,
                ]);
        });
    }
}
