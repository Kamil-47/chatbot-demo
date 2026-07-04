<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'class_number',
        'profile',
        'current_topic',
        'description',
        'notes',
        'next_exam_date',
        'schedule',
        'price_per_lesson'
    ];

    protected $casts = [
        'schedule' => 'array', // {'monday': '15:00', 'wednesday': '16:00'}
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}