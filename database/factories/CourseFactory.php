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
            // Lấy ID giáo viên ngẫu nhiên hoặc tạo mới
            'teacher_id' => User::where('role', 'teacher')->inRandomOrder()->value('id')
                ?? User::factory()->state(['role' => 'teacher'])->create()->id,

            'title' => fake()->jobTitle() . ' Course',
            // ❌ XÓA DÒNG SLUG Ở ĐÂY ĐI VÌ MIGRATION KHÔNG CÓ

            'description' => fake()->paragraph(),

            // ✅ Dùng image_path cho khớp migration
            'image_path' => 'https://loremflickr.com/640/360/tech,programming?random=' . rand(1, 1000),

            'price' => fake()->numberBetween(100000, 2000000),

            // ✅ Dùng is_approved (tinyInteger) cho khớp migration
            'is_approved' => fake()->randomElement([0, 1, 2]),
        ];
    }
}
