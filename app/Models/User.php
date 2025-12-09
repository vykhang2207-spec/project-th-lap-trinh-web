<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Transaction;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Thuộc tính có thể được gán hàng loạt (Mass Assignable).
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Quan trọng: Thêm 'role' cho phân quyền (admin, teacher, student)
        'account_balance',
    ];

    /**
     * Các thuộc tính cần được ẩn khi chuyển đổi thành mảng/JSON.
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Định nghĩa kiểu dữ liệu cho các thuộc tính.
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* =========================================
     * CÁC MỐI QUAN HỆ (RELATIONSHIPS)
     * ========================================= */

    // 1. Một User có nhiều lượt Đăng ký khóa học
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // 2. (Tiện tay thêm luôn) Một User có nhiều Giao dịch
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // 3. (Tiện tay thêm luôn) Một User (Giảng viên) có thể tạo nhiều Khóa học
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    // 4. (Tiện tay thêm luôn) Một User có nhiều tiến độ học (LessonView)

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }
    // 👇 ĐÃ SỬA: Thay LessonView bằng viewedLessons (Quan hệ N-N)
    // Giúp lấy được danh sách các bài học mà User này đã xem
    public function viewedLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_views', 'user_id', 'lesson_id')
            ->withPivot('last_viewed_at');
    }
}
