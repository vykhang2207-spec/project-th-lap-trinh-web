<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            // Lay id giao vien hoac tao moi
            'teacher_id' => User::where('role', 'teacher')->inRandomOrder()->value('id')
                ?? User::factory()->state(['role' => 'teacher'])->create()->id,

            'title' => fake()->jobTitle() . ' Course',
            'description' => fake()->paragraph(),
            'image_path' => 'https://loremflickr.com/640/360/tech,programming?random=' . rand(1, 1000),
            'price' => fake()->numberBetween(100000, 2000000),
            'is_approved' => fake()->randomElement([0, 1, 2]),
        ];
    }
}
