<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $course_id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CourseReactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseReaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseReaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseReaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseReaction whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseReaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseReaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseReaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseReaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseReaction whereUserId($value)
 * @mixin \Eloquent
 */
class CourseReaction extends Model
{
    protected $fillable = ['user_id', 'course_id', 'type']; // type: 'like' hoặc 'dislike'
    /** @use HasFactory<\Database\Factories\CourseReactionFactory> */
    use HasFactory;
}
