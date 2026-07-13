<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    private const NAMES = [
        'Anna Kowalska',
        'Anna Nowak',
        'Katarzyna Wójcik',
        'Magdalena Kaczmarek',
        'Jan Kowalczyk',
        'Piotr Kamiński',
        'Michał Lewandowski',
        'Tomasz Zieliński',
        'Karol Szymański',
    ];

    public function run(): void
    {
        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonth()->format('Y-m');

        foreach (self::NAMES as $name) {
            $student = Student::factory()->create(['name' => $name]);
            $this->seedMonth($student, $previousMonth, isPrevious: true);
            $this->seedMonth($student, $currentMonth, isPrevious: false);
        }
    }

    private function seedMonth(Student $student, string $month, bool $isPrevious): void
    {
        if (empty($student->schedule)) {
            return;
        }

        $startDate = Carbon::parse($month . '-01');
        $endDate = $startDate->copy()->endOfMonth();

        $lessonCount = 0;

        foreach ($student->schedule as $dayName => $time) {
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                if (strtolower($currentDate->format('l')) === $dayName) {
                    Lesson::create([
                        'student_id' => $student->id,
                        'lesson_date' => $currentDate->format('Y-m-d'),
                        'time' => $time,
                        'status' => $this->determineLessonStatus($currentDate, $isPrevious),
                        'month' => $month,
                    ]);
                    $lessonCount++;
                }

                $currentDate->addDay();
            }
        }

        if ($lessonCount === 0) {
            return;
        }

        $isPaid = $isPrevious && rand(1, 100) <= 70;
        $amount = $lessonCount * ($student->price_per_lesson ?? 0);

        Payment::create([
            'student_id' => $student->id,
            'month' => $month,
            'lesson_count' => $lessonCount,
            'amount' => $amount,
            'status' => $isPaid ? 'paid' : 'waiting',
            'payment_date' => $isPaid
                ? $startDate->copy()->addDays(rand(5, 25))->format('Y-m-d')
                : null,
        ]);
    }

    private function determineLessonStatus(Carbon $lessonDate, bool $isPrevious): string
    {
        if ($isPrevious) {
            return rand(1, 100) <= 15 ? 'canceled' : 'completed';
        }

        if ($lessonDate->lt(Carbon::today())) {
            return rand(1, 100) <= 10 ? 'canceled' : 'completed';
        }

        return 'planned';
    }
}
