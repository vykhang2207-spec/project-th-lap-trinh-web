<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;

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
}
