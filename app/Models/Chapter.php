<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $course_id
 * @property string $title
 * @property int $order_index
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Course|null $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Lesson> $lessons
 * @property-read int|null $lessons_count
 * @method static \Database\Factories\ChapterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chapter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chapter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chapter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chapter whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chapter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chapter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chapter whereOrderIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chapter whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chapter whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
