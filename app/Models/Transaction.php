<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;

/**
 * @property int $id
 * @property int $user_id
 * @property int $course_id
 * @property string $total_amount
 * @property string $tax_amount
 * @property string $admin_fee
 * @property string $teacher_earning
 * @property string $payment_method
 * @property string $status
 * @property string|null $transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Course|null $course
 * @property-read User|null $user
 * @method static \Database\Factories\TransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereAdminFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTeacherEarning($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUserId($value)
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use HasFactory;

    /**
     * Đảm bảo tất cả các trường quan trọng từ MoMo callback có thể được lưu.
     */
    // App/Models/Transaction.php
    protected $fillable = [
        'user_id',
        'course_id',
        'total_amount',
        'tax_amount',
        'admin_fee',
        'teacher_earning',
        'status',
        'transaction_id'
    ];
    /**
     * Giao dịch thuộc về một Học viên
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Giao dịch liên quan đến Khóa học nào
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    // Scope lấy các giao dịch chưa trả tiền cho GV
    public function scopePendingPayout($query)
    {
        return $query->where('status', 'success')->where('payout_status', 'pending');
    }
}
