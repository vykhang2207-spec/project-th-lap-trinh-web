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
        $chapterId = Chapter::inRandomOrder()->value('id') ?? Chapter::factory()->create()->id;

        // Danh sach video hoc tap that tu Net Ninja
        $netNinjaVideos = [
            'https://www.youtube.com/embed/DKnn8TlJ4MA',
            'https://www.youtube.com/embed/RT0DZYYE3wc',
            'https://www.youtube.com/embed/thOAk7dhn1c',
            'https://www.youtube.com/embed/cNE0HIRpeiU',
            'https://www.youtube.com/embed/xXOJCHWV6dU',
            'https://www.youtube.com/embed/giMnl4gpZ_I',
            'https://www.youtube.com/embed/cjb1PdA_ZJw',
            'https://www.youtube.com/embed/PgeP3vsWbTc',
            'https://www.youtube.com/embed/0LCAS5WXnL4',
            'https://www.youtube.com/embed/vG3GcBDB9rs',
            'https://www.youtube.com/embed/tV9Hb0A-lzc',
            'https://www.youtube.com/embed/HNTsM2ZmoFQ',
            'https://www.youtube.com/embed/UPuULGjWRsQ',
            'https://www.youtube.com/embed/awStsyqYcbc',
            'https://www.youtube.com/embed/tPhp3PunmuU',
            'https://www.youtube.com/embed/LTvzvQhielk',
            'https://www.youtube.com/embed/HdvFVMaaT4Y',
            'https://www.youtube.com/embed/jRXDwAuiGZY',
            'https://www.youtube.com/embed/5e8W93UQ_98',
            'https://www.youtube.com/embed/J5mBbi-ndY0',
            'https://www.youtube.com/embed/TIktqsjpIik',
            'https://www.youtube.com/embed/hN0OM8eIWdU',
            'https://www.youtube.com/embed/YkwbsesO3Rs',
        ];

        return [
            'chapter_id' => $chapterId,
            'title' => fake()->randomElement(['Introduction to ', 'Setting up ', 'Advanced ', 'Mastering ']) . fake()->word() . ' ' . fake()->randomElement(['Concept', 'Pattern', 'Workflow', 'Basics']),
            'video_url' => fake()->randomElement($netNinjaVideos),
            'order_index' => fake()->numberBetween(1, 15),
        ];
    }
}
