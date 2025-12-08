<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;

class ChapterSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả Khóa học
        Course::all()->each(function (Course $course) {
            // Tạo 3-5 Chương cho mỗi khóa
            // Sử dụng sequence để order_index tăng dần: 1, 2, 3...
            Chapter::factory()
                ->count(rand(3, 5))
                ->sequence(fn($sequence) => [
                    'order_index' => $sequence->index + 1,
                    'title' => 'Chương ' . ($sequence->index + 1) . ': Kiến thức nền tảng', // Tiêu đề khớp với số thứ tự
                ])
                ->create([
                    'course_id' => $course->id,
                ]);
        });
    }
}
