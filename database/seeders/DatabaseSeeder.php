<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Transaction;
use App\Models\Enrollment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->warn('------------- BẮT ĐẦU TẠO DỮ LIỆU DEMO CHUẨN -------------');

        // =================================================================
        // PHẦN 1: TẠO USER CỐ ĐỊNH
        // =================================================================

        // 1. ADMIN
        $admin = User::firstOrCreate(['email' => 'admin@test.com'], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 2. GIÁO VIÊN DEMO
        $teacher = User::firstOrCreate(['email' => 'teacher@example.com'], [
            'name' => 'Giáo Viên Demo',
            'password' => Hash::make('12345678'),
            'role' => 'teacher',
            'email_verified_at' => now(),
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '9999999999',
            'bank_account_name' => 'NGUYEN VAN TEACHER',
        ]);

        // 3. HỌC VIÊN DEMO
        $student = User::firstOrCreate(['email' => 'student@example.com'], [
            'name' => 'Học Viên Chăm Chỉ',
            'password' => Hash::make('12345678'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // =================================================================
        // PHẦN 2: TẠO KHÓA HỌC CỐ ĐỊNH (ID: 1)
        // =================================================================

        $course = Course::firstOrCreate(['title' => 'Lập trình Laravel Thực Chiến (Full)'], [
            // ❌ Đã xóa slug
            'teacher_id' => $teacher->id,
            'price' => 2000000,
            'description' => 'Khóa học đầy đủ nhất về Laravel từ cơ bản đến nâng cao.',

            // ✅ Sửa thumbnail -> image_path
            'image_path' => 'https://i.ytimg.com/vi/ImtZ5yENzgE/maxresdefault.jpg',

            // ✅ Sửa is_published -> is_approved (1 = Đã duyệt)
            'is_approved' => 1,
        ]);

        // Tạo Chương & Bài học cho khóa ID 1
        $chapter = Chapter::create([
            'course_id' => $course->id,
            'title' => 'Chương 1: Khởi động',
            'order_index' => 1
        ]);

        Lesson::create([
            'chapter_id' => $chapter->id,
            'title' => 'Bài 1: Giới thiệu khóa học',
            'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE',


            'order_index' => 1
        ]);

        Lesson::create([
            'chapter_id' => $chapter->id,
            'title' => 'Bài 2: Cài đặt môi trường',
            'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE',


            'order_index' => 2
        ]);

        // =================================================================
        // PHẦN 3: TẠO GIAO DỊCH MẪU
        // =================================================================

        if (!Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->exists()) {

            $price = $course->price;
            $tax = $price * 0.10;
            $adminFee = $price * 0.20;
            $teacherEarning = $price - $tax - $adminFee;

            $trans = Transaction::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'total_amount' => $price,
                'tax_amount' => $tax,
                'admin_fee' => $adminFee,
                'teacher_earning' => $teacherEarning,
                'payment_method' => 'momo',
                'status' => 'success',
                'payout_status' => 'pending',
                'transaction_id' => 'MOMO_FIXED_001',
            ]);

            Enrollment::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'created_at' => $trans->created_at,
            ]);

            $this->command->info('-> Đã tạo Transaction mẫu: Học viên mua khóa Laravel.');
        }

        // =================================================================
        // PHẦN 4: DỮ LIỆU NGẪU NHIÊN (Factory)
        // =================================================================

        $this->command->warn('-> Đang sinh thêm dữ liệu ngẫu nhiên...');

        // 10 GV
        User::factory(10)->create(['role' => 'teacher']);

        // 50 HV
        $randomStudents = User::factory(50)->create(['role' => 'student']);

        // 20 Khóa học
        $teachers = User::where('role', 'teacher')->get();

        // Factory Course đã sửa ở bước trước (dùng image_path, is_approved) nên gọi ở đây là an toàn
        Course::factory(20)->make()->each(function ($c) use ($teachers) {
            $c->teacher_id = $teachers->random()->id;
            $c->save();

            $chapters = \App\Models\Chapter::factory(2)->create(['course_id' => $c->id]);
            foreach ($chapters as $ch) {
                \App\Models\Lesson::factory(3)->create(['chapter_id' => $ch->id]);
            }
        });

        // Giao dịch ngẫu nhiên
        $allCourses = Course::all();
        if ($allCourses->isNotEmpty()) {
            foreach ($randomStudents as $rndStudent) {
                if (rand(0, 1)) {
                    $rndCourse = $allCourses->random();

                    $p = $rndCourse->price;
                    $t = $p * 0.1;
                    $a = $p * 0.2;
                    $e = $p - $t - $a;

                    Transaction::create([
                        'user_id' => $rndStudent->id,
                        'course_id' => $rndCourse->id,
                        'total_amount' => $p,
                        'tax_amount' => $t,
                        'admin_fee' => $a,
                        'teacher_earning' => $e,
                        'status' => 'success',
                        'payout_status' => rand(0, 1) ? 'pending' : 'completed',
                        'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
                    ]);

                    Enrollment::firstOrCreate([
                        'user_id' => $rndStudent->id,
                        'course_id' => $rndCourse->id
                    ]);
                }
            }
        }

        $this->command->info('✅ HOÀN TẤT! DỮ LIỆU ĐÃ ĐỒNG NHẤT VỚI MIGRATION.');
        $this->command->info('   - Admin: admin@test.com / password');
        $this->command->info('   - Teacher: teacher@example.com / 12345678');
        $this->command->info('   - Student: student@example.com / 12345678');
    }
}
