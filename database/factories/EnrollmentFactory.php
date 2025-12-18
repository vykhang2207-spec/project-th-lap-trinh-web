<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        $userId = User::where('role', 'student')->inRandomOrder()->value('id') ?? User::factory()->create()->id;
        $courseId = Course::inRandomOrder()->value('id') ?? Course::factory()->create()->id;

        return [
            'user_id' => $userId,
            'course_id' => $courseId,
        ];
    }
}
