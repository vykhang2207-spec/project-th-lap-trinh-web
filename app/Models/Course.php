<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
// 👇 1. Import Facade Auth vào đây để sửa lỗi
use Illuminate\Support\Facades\Auth;

// Import đầy đủ
use App\Models\User;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\Comment;
use App\Models\CourseReaction;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'image_path',
        'price',
        'is_approved',
    ];

    /* =========================================
     * RELATIONSHIPS
     * ========================================= */

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // Quan trọng để đếm tổng số bài học
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Chapter::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(CourseReaction::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(CourseReaction::class)->where('type', 'like');
    }

    public function dislikes(): HasMany
    {
        return $this->hasMany(CourseReaction::class)->where('type', 'dislike');
    }

    // Helper function check nhanh trạng thái của User
    public function isReactedBy(?User $user)
    {
        if (!$user) return null;
        return $this->reactions()->where('user_id', $user->id)->first();
    }

    // 👇 HÀM TÍNH TIẾN ĐỘ ĐÃ SỬA LỖI
    public function progress()
    {
        // 1. Nếu chưa đăng nhập (Guest) -> Tiến độ là 0%
        if (!Auth::check()) {
            return 0;
        }

        // 2. Tổng số bài học của khóa
        $totalLessons = $this->lessons()->count();

        if ($totalLessons == 0) {
            return 0;
        }

        // 3. Số bài đã học
        // Sử dụng Auth::id() thay vì auth()->id() để code chuẩn hơn và IDE không báo lỗi
        $completedLessons = $this->lessons()
            ->join('lesson_views', 'lessons.id', '=', 'lesson_views.lesson_id')
            ->where('lesson_views.user_id', Auth::id())
            ->count();

        // 4. Tính phần trăm
        return round(($completedLessons / $totalLessons) * 100);
    }
}
