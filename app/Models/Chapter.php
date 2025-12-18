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

    // Chuong hoc thuoc ve mot khoa hoc
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Mot chuong co nhieu bai hoc
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}
