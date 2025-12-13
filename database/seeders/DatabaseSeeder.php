<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Transaction;
use App\Models\Enrollment;
use App\Models\Comment;
use App\Models\CourseReaction;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa sạch dữ liệu cũ (nếu chạy riêng seeder mà ko chạy migrate:fresh)
        // DB::table('users')->truncate(); // Cẩn thận lệnh này nếu dùng foreign key

        $this->command->warn('------------- BẮT ĐẦU TẠO DỮ LIỆU DEMO CHUẨN -------------');

        // =================================================================
        // PHẦN 1: TẠO USER CỐ ĐỊNH (Luôn có ID cố định nếu chạy từ DB trắng)
        // =================================================================

        // 1. ADMIN (ID: 1)
        $admin = User::firstOrCreate(['email' => 'admin@test.com'], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 2. GIÁO VIÊN DEMO (ID: 2)
        $teacher = User::firstOrCreate(['email' => 'teacher@example.com'], [
            'name' => 'Giáo Viên Demo',
            'password' => Hash::make('12345678'),
            'role' => 'teacher',
            'email_verified_at' => now(),
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '9999999999',
            'bank_account_name' => 'NGUYEN VAN TEACHER',
        ]);

        // 3. HỌC VIÊN DEMO (ID: 3)
        $student = User::firstOrCreate(['email' => 'student@example.com'], [
            'name' => 'Học Viên Chăm Chỉ',
            'password' => Hash::make('12345678'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // =================================================================
        // PHẦN 2: TẠO KHÓA HỌC CỐ ĐỊNH (LUÔN LÀ ID: 1)
        // =================================================================

        $course = Course::firstOrCreate(['title' => 'Lập trình Laravel Thực Chiến (Full)'], [
            'slug' => 'lap-trinh-laravel-thuc-chien-full', // Slug cố định
            'teacher_id' => $teacher->id,
            'price' => 2000000, // 2 triệu
            'description' => 'Khóa học đầy đủ nhất về Laravel từ cơ bản đến nâng cao.',
            'thumbnail' => 'https://i.ytimg.com/vi/ImtZ5yENzgE/maxresdefault.jpg', // Ảnh thật cho đẹp
            'is_published' => true,
        ]);

        // Tạo Chương & Bài học cho khóa ID 1 này
        $chapter = Chapter::create([
            'course_id' => $course->id,
            'title' => 'Chương 1: Khởi động',
            'order' => 1
        ]);

        Lesson::create([
            'chapter_id' => $chapter->id,
            'title' => 'Bài 1: Giới thiệu khóa học',
            'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE', // Link video thật
            'duration' => 10,
            'is_preview' => true,
            'order' => 1
        ]);

        Lesson::create([
            'chapter_id' => $chapter->id,
            'title' => 'Bài 2: Cài đặt môi trường',
            'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE',
            'duration' => 15,
            'is_preview' => false,
            'order' => 2
        ]);

        // =================================================================
        // PHẦN 3: TẠO GIAO DỊCH MẪU (Học viên ID 3 mua Khóa ID 1)
        // =================================================================

        // Kiểm tra xem đã mua chưa, chưa thì tạo
        if (!Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->exists()) {

            // Tính toán tiền
            $price = $course->price;
            $tax = $price * 0.10;
            $adminFee = $price * 0.20;
            $teacherEarning = $price - $tax - $adminFee;

            // Tạo Transaction
            $trans = Transaction::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'total_amount' => $price,
                'tax_amount' => $tax,
                'admin_fee' => $adminFee,
                'teacher_earning' => $teacherEarning,
                'payment_method' => 'momo',
                'status' => 'success',
                'payout_status' => 'pending', // Treo tiền chờ Admin trả
                'transaction_id' => 'MOMO_FIXED_001',
            ]);

            // Cấp quyền học
            Enrollment::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'created_at' => $trans->created_at,
            ]);

            $this->command->info('-> Đã tạo Transaction mẫu: Học viên mua khóa Laravel.');
        }

        // =================================================================
        // PHẦN 4: TẠO DỮ LIỆU RÁC (FILLER) ĐỂ DANH SÁCH KHÔNG BỊ TRỐNG
        // =================================================================

        $this->command->warn('-> Đang sinh thêm 50 users và 20 khóa học ngẫu nhiên...');

        // Tạo thêm 10 GV ngẫu nhiên
        User::factory(10)->create(['role' => 'teacher']);

        // Tạo thêm 50 HV ngẫu nhiên
        $randomStudents = User::factory(50)->create(['role' => 'student']);

        // Tạo thêm 20 Khóa học ngẫu nhiên (Gắn cho GV bất kỳ)
        $teachers = User::where('role', 'teacher')->get();

        Course::factory(20)->make()->each(function ($c) use ($teachers) {
            $c->teacher_id = $teachers->random()->id;
            $c->save();

            // Tạo chương/bài cho mỗi khóa
            $chapters = \App\Models\Chapter::factory(2)->create(['course_id' => $c->id]);
            foreach ($chapters as $ch) {
                \App\Models\Lesson::factory(3)->create(['chapter_id' => $ch->id]);
            }
        });

        // Tạo thêm giao dịch ngẫu nhiên để Admin Dashboard có số liệu
        $this->command->info('-> Đang sinh giao dịch ngẫu nhiên...');
        $allCourses = Course::all();

        foreach ($randomStudents as $rndStudent) {
            if (rand(0, 1)) { // 50% học viên sẽ mua khóa học
                $rndCourse = $allCourses->random();

                // Logic tính tiền
                $p = $rndCourse->price;
                $t = $p * 0.1;
                $a = $p * 0.2;
                $e = $p - $t - $a;

                $tr = Transaction::create([
                    'user_id' => $rndStudent->id,
                    'course_id' => $rndCourse->id,
                    'total_amount' => $p,
                    'tax_amount' => $t,
                    'admin_fee' => $a,
                    'teacher_earning' => $e,
                    'status' => 'success',
                    'payout_status' => rand(0, 1) ? 'pending' : 'completed', // Random trạng thái trả lương
                    'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
                ]);

                Enrollment::firstOrCreate([
                    'user_id' => $rndStudent->id,
                    'course_id' => $rndCourse->id
                ]);
            }
        }

        $this->command->info('✅ HOÀN TẤT! DỮ LIỆU ĐÃ ĐỒNG NHẤT.');
        $this->command->info('   - Admin: admin@test.com / password');
        $this->command->info('   - Teacher: teacher@example.com / 12345678');
        $this->command->info('   - Student: student@example.com / 12345678');
        $this->command->info('   - Khóa học Demo ID: 1 (Luôn luôn là 1)');
    }
}
