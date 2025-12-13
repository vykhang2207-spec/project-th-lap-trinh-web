<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Transaction;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $account_balance
 * @property string $password
 * @property string|null $remember_token
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Lesson> $viewedLessons
 * @property-read int|null $viewed_lessons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Withdrawal> $withdrawals
 * @property-read int|null $withdrawals_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAccountBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */ class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Các thuộc tính cho phép gán (Mass Assignable)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // admin, teacher, student

        // 👇 THÔNG TIN NGÂN HÀNG (MỚI)
        // Để Admin biết đường chuyển khoản trả lương cuối tháng
        'bank_name',
        'bank_account_number',
        'bank_account_name',

        // ❌ ĐÃ XÓA: 'account_balance' (Không dùng ví nữa)
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* =========================================
     * CÁC MỐI QUAN HỆ (RELATIONSHIPS)
     * ========================================= */

    // 1. Một User có nhiều lượt Đăng ký khóa học
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // 2. Một User có nhiều Giao dịch (Lịch sử mua hàng)
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // 3. Một User (Giảng viên) có thể tạo nhiều Khóa học
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    // 4. Các bài học đã xem (Để tính tiến độ)
    public function viewedLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_views', 'user_id', 'lesson_id')
            ->withPivot('last_viewed_at');
    }

    // 👇 5. QUAN HỆ MỚI: Lịch sử nhận lương (Payouts)
    // Thay thế cho withdrawals cũ
    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class, 'teacher_id');
    }
}
