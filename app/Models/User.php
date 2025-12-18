<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Transaction;
use App\Models\Lesson;
use App\Models\Payout;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // admin, teacher, student
        // Thong tin ngan hang
        'bank_name',
        'bank_account_number',
        'bank_account_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Danh sach khoa hoc da dang ky
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // Lich su giao dich cua user
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Danh sach khoa hoc giao vien tao ra
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    // Bai hoc da xem (de tinh tien do)
    public function viewedLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_views', 'user_id', 'lesson_id')
            ->withPivot('last_viewed_at');
    }

    // Lich su nhan luong cua giao vien
    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class, 'teacher_id');
    }

    // Lay toan bo giao dich ban khoa hoc cua giao vien nay
    public function sales(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaction::class,
            Course::class,
            'teacher_id',
            'course_id',
            'id',
            'id'
        );
    }
}
