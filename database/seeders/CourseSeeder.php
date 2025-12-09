<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy danh sách ID của giảng viên đã tạo ở UserSeeder
        $teacherIds = User::where('role', 'teacher')->pluck('id');

        if ($teacherIds->isEmpty()) {
            // Fallback nếu chưa có teacher nào
            $teacherIds = User::factory()->teacher()->count(5)->create()->pluck('id');
        }

        // Tạo 30 khóa học, gán random cho các giảng viên này
        Course::factory()->count(30)->make()->each(function ($course) use ($teacherIds) {
            $course->teacher_id = $teacherIds->random();
            $course->save();
        });
    }
}
