<?php

namespace Database\Factories;

use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChapterFactory extends Factory
{
    protected $model = Chapter::class;

    public function definition(): array
    {
        // Lay id khoa hoc co san hoac tao moi
        $courseId = Course::inRandomOrder()->value('id') ?? Course::factory()->create()->id;

        return [
            'course_id' => $courseId,
            'title' => 'Chapter ' . fake()->randomNumber(1, 2) . ': ' . fake()->word(),
            'order_index' => fake()->numberBetween(1, 10),
        ];
    }
}
