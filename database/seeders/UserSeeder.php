<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo 1 Admin cứng để test
        User::factory()->admin()->create([
            'name' => 'Admin Boss',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        // 2. Tạo 10 Giảng viên
        User::factory()->teacher()->count(10)->create();

        // 3. Tạo 50 Học viên
        User::factory()->count(50)->create();
    }
}
