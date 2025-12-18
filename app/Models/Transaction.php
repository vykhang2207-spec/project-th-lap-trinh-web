<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;

class Transaction extends Model
{
    use HasFactory;

    // Cac truong duoc phep luu vao db
    protected $fillable = [
        'user_id',
        'course_id',
        'total_amount',
        'tax_amount',
        'admin_fee',
        'teacher_earning',
        'status',
        'transaction_id',
        'payout_status',
        'payment_method'
    ];

    // Giao dich thuoc ve mot user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Giao dich thuoc ve mot khoa hoc
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // Scope lay cac giao dich chua thanh toan cho giao vien
    public function scopePendingPayout($query)
    {
        return $query->where('status', 'success')->where('payout_status', 'pending');
    }
}
