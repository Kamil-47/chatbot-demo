<?php

namespace App\Models;

use App\Enums\LessonStatus;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'student_id',
        'lesson_date',
        'time',
        'status',
        'month'
    ];

    protected $casts = [
        'status' => LessonStatus::class,
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}