<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'student',

            // Thong tin ngan hang
            'bank_name' => fake()->randomElement(['Vietcombank', 'MBBank', 'Techcombank', 'ACB', 'BIDV', 'VPBank']),
            'bank_account_number' => fake()->numerify('##########'),
            'bank_account_name' => function (array $attributes) {
                return strtoupper($attributes['name']);
            },
        ];
    }

    // State cho giao vien
    public function teacher(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'teacher',
        ]);
    }

    // State cho admin
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
        ]);
    }

    // State chua xac thuc email
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
