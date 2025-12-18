<?php

namespace App\Models;

use App\Models\Chapter;
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

    // Bai hoc thuoc ve mot chuong
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    // Bai hoc co nhieu nguoi xem (quan he n-n qua bang lesson_views)
    public function viewers()
    {
        return $this->belongsToMany(User::class, 'lesson_views', 'lesson_id', 'user_id');
    }
}
