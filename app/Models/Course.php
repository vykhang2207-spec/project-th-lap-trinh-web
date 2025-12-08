<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough; // Import thêm cái này

// Import đầy đủ các Model liên quan
use App\Models\User;
use App\Models\Chapter;
use App\Models\Lesson;          // Thêm Lesson
use App\Models\Enrollment;
use App\Models\Comment;         // Thêm Comment
use App\Models\CourseReaction;  // Thêm CourseReaction (Thay vì Reaction)

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
     * ĐỊNH NGHĨA MỐI QUAN HỆ (RELATIONSHIPS)
     * ========================================= */

    // 1. Khóa học thuộc về một Giảng viên
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // 2. Khóa học có nhiều Chương học
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    // 3. Khóa học có nhiều Học viên đã đăng ký
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // 4. Lấy TẤT CẢ Bài học của Khóa (Xuyên qua bảng Chapter)
    // Giúp tính tổng view dễ dàng hơn: $course->lessons
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Chapter::class);
    }

    // 5. Khóa học có nhiều Bình luận
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // 6. Lấy danh sách Likes (Từ bảng course_reactions)
    public function likes(): HasMany
    {
        return $this->hasMany(CourseReaction::class)->where('type', 'like');
    }

    // 7. Lấy danh sách Dislikes (Từ bảng course_reactions)
    public function dislikes(): HasMany
    {
        return $this->hasMany(CourseReaction::class)->where('type', 'dislike');
    }
}
