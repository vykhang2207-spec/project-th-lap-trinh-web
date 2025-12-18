<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = ['teacher_id', 'amount', 'batch_id', 'status', 'paid_at', 'note'];

    // Payout thuoc ve mot giao vien
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
