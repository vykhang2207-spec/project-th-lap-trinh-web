<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        // Đảm bảo lấy ID của Chương học có sẵn
        $chapterId = Chapter::inRandomOrder()->value('id') ?? Chapter::factory()->create()->id;

        // Danh sách video thật từ kênh Net Ninja (Dạng Embed Link)
        $netNinjaVideos = [
            // Laravel 10/11 Tutorial
            'https://www.youtube.com/embed/qS0yXqTRk0Y',
            'https://www.youtube.com/embed/376MG1k0nCQ',

            // Full Modern React Tutorial
            'https://www.youtube.com/embed/j942wKiXFu8',
            'https://www.youtube.com/embed/kVeOpcw4LCY',

            // Vue 3 Tutorial
            'https://www.youtube.com/embed/Yb9O4a3X4TI',

            // Flutter for Beginners
            'https://www.youtube.com/embed/1xipg02Wu8s',

            // HTML & CSS Crash Course
            'https://www.youtube.com/embed/hu-q2zYwEYs',
            'https://www.youtube.com/embed/PMvX07P18VQ',

            // Node.js Crash Course
            'https://www.youtube.com/embed/zb3Qk8SG5Ms',

            // Modern JavaScript
            'https://www.youtube.com/embed/iWOYAxlnaww',
        ];

        return [
            'chapter_id' => $chapterId,
            // Tạo tiêu đề bài học nghe cho "nguy hiểm" một chút
            'title' => fake()->randomElement(['Introduction to ', 'Setting up ', 'Advanced ', 'Mastering ']) . fake()->word() . ' ' . fake()->randomElement(['Concept', 'Pattern', 'Workflow', 'Basics']),

            // Chọn ngẫu nhiên 1 video từ danh sách trên
            'video_url' => fake()->randomElement($netNinjaVideos),

            // Số thứ tự (Seeder sẽ ghi đè lại cái này cho đẹp sau)
            'order_index' => fake()->numberBetween(1, 15),
        ];
    }
}
