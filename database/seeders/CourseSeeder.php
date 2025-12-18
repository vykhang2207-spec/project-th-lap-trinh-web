<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Lay danh sach id giao vien
        $teacherIds = User::where('role', 'teacher')->pluck('id');

        if ($teacherIds->isEmpty()) {
            // Tao moi neu chua co giao vien
            $teacherIds = User::factory()->teacher()->count(5)->create()->pluck('id');
        }

        // Tao 30 khoa hoc va gan ngau nhien cho giao vien
        Course::factory()->count(30)->make()->each(function ($course) use ($teacherIds) {
            $course->teacher_id = $teacherIds->random();
            $course->save();
        });
    }
}
