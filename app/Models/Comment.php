<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_id', 'content'];

    // Binh luan thuoc ve mot user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Binh luan thuoc ve mot khoa hoc
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
