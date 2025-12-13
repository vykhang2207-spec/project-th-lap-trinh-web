<?php

namespace App\Models;

use App\Models\Chapter;
use App\Models\LessonView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $chapter_id
 * @property string $title
 * @property string $video_url
 * @property int $order_index
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Chapter|null $chapter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $viewers
 * @property-read int|null $viewers_count
 * @method static \Database\Factories\LessonFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereChapterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereOrderIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereVideoUrl($value)
 * @mixin \Eloquent
 */
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
