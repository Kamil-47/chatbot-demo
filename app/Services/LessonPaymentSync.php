<?php

namespace App\Services;

use App\Enums\LessonStatus;
use App\Enums\PaymentStatus;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Student;

class LessonPaymentSync
{
    /**
     * Przelicza sumę do zapłaty dla ucznia w danym miesiącu na podstawie
     * lekcji nie-odwołanych. Tworzy Payment jeśli jeszcze nie istnieje.
     */
    public function syncMonth(Student $student, string $month): void
    {
        $activeLessons = Lesson::where('student_id', $student->id)
            ->where('month', $month)
            ->where('status', '!=', LessonStatus::Canceled)
            ->count();

        $amount = $activeLessons * ($student->price_per_lesson ?? 0);

        $payment = Payment::where('student_id', $student->id)
            ->where('month', $month)
            ->first();

        if ($payment) {
            $payment->update([
                'lesson_count' => $activeLessons,
                'amount' => $amount,
            ]);
            return;
        }

        Payment::create([
            'student_id' => $student->id,
            'month' => $month,
            'lesson_count' => $activeLessons,
            'amount' => $amount,
            'status' => PaymentStatus::Waiting,
        ]);
    }
}
