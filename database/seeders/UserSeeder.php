<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. TẠO ADMIN (Sếp)
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            // Admin không cần bank info cũng được
        ]);

        // 2. TẠO GIÁO VIÊN MẪU (Để bạn đăng nhập test)
        User::create([
            'name' => 'Giáo Viên Mẫu',
            'email' => 'teacher@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'teacher',
            'email_verified_at' => now(),

            // 👇 THÔNG TIN NGÂN HÀNG CỐ ĐỊNH
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '0123456789',
            'bank_account_name' => 'GIAO VIEN MAU',
        ]);

        // 3. TẠO THÊM 10 GIÁO VIÊN NGẪU NHIÊN
        // (Factory đã cấu hình ở bước 1 sẽ tự điền Bank Info)
        User::factory(10)->create([
            'role' => 'teacher',
        ]);

        // 4. TẠO 50 HỌC VIÊN NGẪU NHIÊN
        User::factory(50)->create([
            'role' => 'student',
            // Học viên có bank info hay không không quan trọng, để factory tự random
        ]);
    }
}
