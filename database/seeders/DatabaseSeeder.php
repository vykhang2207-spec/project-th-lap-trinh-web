<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Transaction;
use App\Models\Enrollment;
use App\Models\Payout;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');

        // Danh sach video embed
        $realVideos = [
            'https://www.youtube.com/embed/DKnn8TlJ4MA',
            'https://www.youtube.com/embed/RT0DZYYE3wc',
            'https://www.youtube.com/embed/thOAk7dhn1c',
            'https://www.youtube.com/embed/cNE0HIRpeiU',
            'https://www.youtube.com/embed/xXOJCHWV6dU',
            'https://www.youtube.com/embed/giMnl4gpZ_I',
            'https://www.youtube.com/embed/cjb1PdA_ZJw',
            'https://www.youtube.com/embed/PgeP3vsWbTc',
            'https://www.youtube.com/embed/0LCAS5WXnL4',
            'https://www.youtube.com/embed/vG3GcBDB9rs',
            'https://www.youtube.com/embed/tV9Hb0A-lzc',
            'https://www.youtube.com/embed/HNTsM2ZmoFQ',
            'https://www.youtube.com/embed/UPuULGjWRsQ',
            'https://www.youtube.com/embed/awStsyqYcbc',
            'https://www.youtube.com/embed/tPhp3PunmuU',
            'https://www.youtube.com/embed/LTvzvQhielk',
            'https://www.youtube.com/embed/HdvFVMaaT4Y',
            'https://www.youtube.com/embed/jRXDwAuiGZY',
            'https://www.youtube.com/embed/5e8W93UQ_98',
            'https://www.youtube.com/embed/J5mBbi-ndY0',
            'https://www.youtube.com/embed/TIktqsjpIik',
            'https://www.youtube.com/embed/hN0OM8eIWdU',
            'https://www.youtube.com/embed/YkwbsesO3Rs',
        ];

        // Tao admin
        User::firstOrCreate(['email' => 'admin@test.com'], [
            'name' => 'Super Administrator',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Tao giao vien chinh
        $mainTeacher = User::firstOrCreate(['email' => 'teacher@test.com'], [
            'name' => 'Giao Vien Demo (Co Tien)',
            'password' => Hash::make('12345678'),
            'role' => 'teacher',
            'email_verified_at' => now(),
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '9999999999',
            'bank_account_name' => 'NGUYEN VAN TEACHER',
        ]);

        // Tao hoc vien chinh
        $mainStudent = User::firstOrCreate(['email' => 'student@test.com'], [
            'name' => 'Hoc Vien Demo',
            'password' => Hash::make('12345678'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Tao du lieu nguoi dung ngau nhien
        $teachers = User::factory(10)->teacher()->create();
        $students = User::factory(50)->create(['role' => 'student']);

        $teachers->push($mainTeacher);
        $students->push($mainStudent);

        // Tao khoa hoc, chuong va bai hoc
        foreach ($teachers as $teacher) {
            for ($i = 0; $i < rand(2, 3); $i++) {
                $price = rand(2, 20) * 100000;

                $course = Course::create([
                    'teacher_id' => $teacher->id,
                    'title' => $faker->jobTitle . ' - ' . $faker->word,
                    'description' => $faker->paragraph(3),
                    'image_path' => 'https://loremflickr.com/640/360/tech?random=' . rand(1, 999),
                    'price' => $price,
                    'is_approved' => 1,
                    'created_at' => $faker->dateTimeBetween('-1 year', '-1 month'),
                ]);

                for ($c = 1; $c <= rand(3, 5); $c++) {
                    $chapter = Chapter::create([
                        'course_id' => $course->id,
                        'title' => "Chuong $c: " . $faker->realText(20),
                        'order_index' => $c,
                    ]);

                    for ($l = 1; $l <= rand(3, 6); $l++) {
                        Lesson::create([
                            'chapter_id' => $chapter->id,
                            'title' => "Bai $l: " . $faker->realText(30),
                            'video_url' => $faker->randomElement($realVideos),
                            'order_index' => $l,
                        ]);
                    }
                }
            }
        }

        // Tao giao dich va enrollment
        $TAX_RATE = 0.10;
        $FEE_RATE = 0.20;

        foreach ($teachers as $teacher) {
            $courses = Course::where('teacher_id', $teacher->id)->get();
            if ($courses->isEmpty()) continue;

            $paidAmountAccumulator = 0;

            // Giao dich da hoan thanh
            for ($k = 0; $k < rand(2, 5); $k++) {
                $student = $students->random();
                $course = $courses->random();

                if (Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->exists()) continue;

                $amount = $course->price;
                $tax = $amount * $TAX_RATE;
                $fee = $amount * $FEE_RATE;
                $earning = $amount - $tax - $fee;

                Transaction::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'total_amount' => $amount,
                    'tax_amount' => $tax,
                    'admin_fee' => $fee,
                    'teacher_earning' => $earning,
                    'payment_method' => 'vnpay',
                    'status' => 'success',
                    'transaction_id' => 'TRANS_OLD_' . Str::random(8),
                    'payout_status' => 'completed',
                    'created_at' => now()->subMonths(2),
                ]);

                Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id]);
                $paidAmountAccumulator += $earning;
            }

            // Tao lich su payout
            if ($paidAmountAccumulator > 0) {
                Payout::create([
                    'teacher_id' => $teacher->id,
                    'amount' => $paidAmountAccumulator,
                    'batch_id' => 'PAY_BATCH_' . Str::random(6),
                    'status' => 'completed',
                    'paid_at' => now()->subMonth(),
                    'note' => 'Quyet toan thang truoc'
                ]);
            }

            // Giao dich chua thanh toan (Pending)
            $pendingCount = ($teacher->email === 'teacher@test.com') ? 10 : rand(0, 2);

            for ($k = 0; $k < $pendingCount; $k++) {
                $student = $students->random();
                $course = $courses->random();

                if (Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->exists()) continue;

                $amount = $course->price;
                $tax = $amount * $TAX_RATE;
                $fee = $amount * $FEE_RATE;
                $earning = $amount - $tax - $fee;

                Transaction::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'total_amount' => $amount,
                    'tax_amount' => $tax,
                    'admin_fee' => $fee,
                    'teacher_earning' => $earning,
                    'payment_method' => 'momo',
                    'status' => 'success',
                    'transaction_id' => 'TRANS_NEW_' . Str::random(8),
                    'payout_status' => 'pending',
                    'created_at' => now()->subDays(rand(1, 10)),
                ]);

                Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id]);
            }
        }

        // Tao du lieu xem bai hoc
        $allLessons = Lesson::pluck('id');
        foreach ($students as $student) {
            $randomLessons = $allLessons->random(min($allLessons->count(), rand(5, 10)));
            foreach ($randomLessons as $lessonId) {
                DB::table('lesson_views')->updateOrInsert(
                    ['user_id' => $student->id, 'lesson_id' => $lessonId],
                    ['last_viewed_at' => now()]
                );
            }
        }
    }
}
