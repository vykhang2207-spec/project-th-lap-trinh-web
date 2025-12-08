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
        // Đảm bảo lấy ID của Giảng viên có sẵn hoặc tạo mới nếu chưa có
        $teacherId = User::where('role', 'teacher')->inRandomOrder()->value('id') ?? User::factory()->teacher()->create()->id;

        return [
            'teacher_id' => $teacherId,
            'title' => fake()->jobTitle() . ' Mastery Course',
            'description' => fake()->realText(500),
            'image_path' => 'https://loremflickr.com/640/360/tech,programming?random=' . rand(1, 1000),
            'price' => fake()->numberBetween(100000, 1000000),
            // 80% khóa học được duyệt, 20% chờ duyệt
            'is_approved' => fake()->boolean(80),
        ];
    }
}
