<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Tao tai khoan admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Tao tai khoan giao vien mau
        User::create([
            'name' => 'Giáo Viên Mẫu',
            'email' => 'teacher@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'teacher',
            'email_verified_at' => now(),

            // Thong tin ngan hang co dinh
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '0123456789',
            'bank_account_name' => 'GIAO VIEN MAU',
        ]);

        // Tao them 10 giao vien
        User::factory(10)->create([
            'role' => 'teacher',
        ]);

        // Tao 50 hoc vien
        User::factory(50)->create([
            'role' => 'student',
        ]);
    }
}
