<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseReaction extends Model
{
    // type la 'like' hoac 'dislike'
    protected $fillable = ['user_id', 'course_id', 'type'];

    use HasFactory;
}
