<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import cái này

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_id', 'content'];

    // 👇 BẠN ĐANG THIẾU ĐOẠN NÀY 👇
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
