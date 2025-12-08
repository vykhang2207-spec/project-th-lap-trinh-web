<?php

namespace App\Models;

use App\Models\Chapter;
use App\Models\LessonView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = [
        'chapter_id',
        'title',
        'video_url',
        'order_index',
    ];
    // 1. Bài giảng thuộc về một Chương học
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    // 2. Bài giảng có nhiều Lượt xem
    public function viewers()
    {
        return $this->belongsToMany(User::class, 'lesson_views', 'lesson_id', 'user_id');
    }
}
