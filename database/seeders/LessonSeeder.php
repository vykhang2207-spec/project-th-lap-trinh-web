<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chapter;
use App\Models\Lesson;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả Chương học
        Chapter::all()->each(function (Chapter $chapter) {
            // Tạo 3-8 Bài giảng cho mỗi chương
            // order_index tăng dần: 1, 2, 3...
            Lesson::factory()
                ->count(rand(3, 8))
                ->sequence(fn($sequence) => [
                    'order_index' => $sequence->index + 1,
                    'title' => 'Bài ' . ($sequence->index + 1) . ': Hướng dẫn chi tiết', // Tiêu đề khớp số
                ])
                ->create([
                    'chapter_id' => $chapter->id,
                ]);
        });
    }
}
