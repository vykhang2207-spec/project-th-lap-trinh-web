<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseReaction extends Model
{
    protected $fillable = ['user_id', 'course_id', 'type']; // type: 'like' hoặc 'dislike'
    /** @use HasFactory<\Database\Factories\CourseReactionFactory> */
    use HasFactory;
}
