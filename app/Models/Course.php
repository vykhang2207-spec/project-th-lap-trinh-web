<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;

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

    // Khoa hoc thuoc ve mot giao vien
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Khoa hoc co nhieu chuong
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    // Khoa hoc co nhieu hoc vien dang ky
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // Lay tat ca bai hoc thong qua bang chapters
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Chapter::class);
    }

    // Khoa hoc co nhieu binh luan
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // Cac luot tuong tac (like/dislike)
    public function reactions(): HasMany
    {
        return $this->hasMany(CourseReaction::class);
    }

    // Lay danh sach like
    public function likes(): HasMany
    {
        return $this->hasMany(CourseReaction::class)->where('type', 'like');
    }

    // Lay danh sach dislike
    public function dislikes(): HasMany
    {
        return $this->hasMany(CourseReaction::class)->where('type', 'dislike');
    }

    // Kiem tra user da like hay dislike chua
    public function isReactedBy(?User $user)
    {
        if (!$user) return null;
        return $this->reactions()->where('user_id', $user->id)->first();
    }

    // Tinh phan tram tien do hoc tap
    public function progress()
    {
        // Chua dang nhap thi 0%
        if (!Auth::check()) {
            return 0;
        }

        // Tong so bai hoc
        $totalLessons = $this->lessons()->count();

        if ($totalLessons == 0) {
            return 0;
        }

        // Dem so bai da hoc xong
        $completedLessons = $this->lessons()
            ->join('lesson_views', 'lessons.id', '=', 'lesson_views.lesson_id')
            ->where('lesson_views.user_id', Auth::id())
            ->count();

        // Tinh phan tram
        return round(($completedLessons / $totalLessons) * 100);
    }
}
