<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'amount',
        'payment_date',
        'month',
        'status',
        'lesson_count'
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}