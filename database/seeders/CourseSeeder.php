<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 30 Khóa học, mỗi khóa tự động chọn một Giảng viên ngẫu nhiên
        Course::factory()->count(30)->create();
    }
}
