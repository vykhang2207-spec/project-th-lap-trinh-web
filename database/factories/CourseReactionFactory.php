<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CourseReactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['like', 'dislike']),
        ];
    }
}
