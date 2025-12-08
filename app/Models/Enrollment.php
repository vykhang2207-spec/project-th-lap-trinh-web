<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
    ];

    /**
     * Bản ghi đăng ký thuộc về một Học viên
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Bản ghi đăng ký liên quan đến một Khóa học
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
