<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chapter extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id',
        'title',
        'order_index',
    ];
    // 1. Chương học thuộc về một Khóa học
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // 2. Chương học có nhiều Bài giảng
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}
