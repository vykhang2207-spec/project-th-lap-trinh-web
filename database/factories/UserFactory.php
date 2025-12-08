<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User; // Đảm bảo đã import Model User

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Gắn Model User vào Factory.
     */
    protected $model = User::class;

    /**
     * Mật khẩu hiện tại đang được sử dụng bởi factory.
     */
    protected static ?string $password;

    /**
     * Định nghĩa trạng thái mặc định của model (là 'student').
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),

            // Hash mật khẩu, sử dụng lại giá trị nếu đã hash
            'password' => static::$password ??= Hash::make('password'),

            // Thêm vai trò mặc định là student
            'role' => 'student',
        ];
    }

    /* =========================================
     * ĐỊNH NGHĨA CÁC TRẠNG THÁI VAI TRÒ
     * ========================================= */

    /**
     * Tạo trạng thái để tạo Giảng viên (teacher).
     */
    public function teacher(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'teacher',
        ]);
    }

    /**
     * Tạo trạng thái để tạo Quản trị viên (admin).
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
        ]);
    }
    
    /* =========================================
     * TRẠNG THÁI MẶC ĐỊNH LARAVEL
     * ========================================= */

    /**
     * Chỉ ra rằng địa chỉ email của model chưa được xác minh.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
