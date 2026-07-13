<?php

namespace App\Http\Controllers;

use App\Enums\LessonStatus;
use App\Models\Lesson;
use App\Models\Student;
use App\Services\LessonPaymentSync;
use App\Support\DateFormat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Carbon\Carbon;

class LessonController extends Controller
{
    public function __construct(private LessonPaymentSync $paymentSync)
    {
    }

    public function index(Request $request)
    {
        $month = $request->month;
        $students = Student::with(['lessons' => function ($q) use ($month) {
            if ($month) {
                $q->where('month', $month);
            }
        }])->get();

        $studentsData = $students->map(function ($student) {
            $lessons = $student->lessons;
            return [
                'id' => $student->id,
                'name' => $student->name,
                'total' => $lessons->count(),
                'completed' => $lessons->where('status', LessonStatus::Completed)->count(),
                'canceled' => $lessons->where('status', LessonStatus::Canceled)->count(),
                'planned' => $lessons->where('status', LessonStatus::Planned)->count(),
            ];
        })->filter(fn($s) => $s['total'] > 0);

        return view('lesson.index', compact('studentsData', 'month'));
    }

    public function show($studentId, Request $request)
    {
        $student = Student::findOrFail($studentId);
        $lessons = Lesson::where('student_id', $studentId)
            ->when($request->month, function ($q, $month) {
                return $q->where('month', $month);
            })
            ->orderBy('lesson_date')
            ->get()
            ->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'student_id' => $lesson->student_id,
                    'date' => DateFormat::pl($lesson->lesson_date),
                    'time' => $lesson->time,
                    'status' => $lesson->status,
                    'month' => $lesson->month,
                ];
            });

        return view('lesson.show', compact('student', 'lessons'));
    }

    public function edit($id)
    {
        $lesson = Lesson::with('student')->findOrFail($id);
        return view('lesson.edit', compact('lesson'));
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $data = $request->validate([
            'lesson_date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'status' => ['required', new Enum(LessonStatus::class)],
        ]);

        $oldMonth = $lesson->month;
        $newMonth = Carbon::parse($data['lesson_date'])->format('Y-m');

        $lesson->update([
            'lesson_date' => $data['lesson_date'],
            'time' => $data['time'],
            'status' => $data['status'],
            'month' => $newMonth,
        ]);

        $this->paymentSync->syncMonth($lesson->student, $newMonth);

        // Jeżeli lekcja przeniesiona do innego miesiąca — zsynchronizuj też stary miesiąc,
        // bo lekcja została w nim policzona przed zmianą.
        if ($oldMonth !== $newMonth) {
            $this->paymentSync->syncMonth($lesson->student, $oldMonth);
        }

        return redirect()->route('lesson.show', $lesson->student_id)
            ->with('month', $lesson->month);
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'month' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $month = $data['month'] ?? now()->format('Y-m');
        $students = Student::whereNotNull('schedule')->get();

        foreach ($students as $student) {
            if (empty($student->schedule)) {
                continue;
            }

            $this->regenerateStudentLessons($student, $month);
        }

        return redirect()->route('lesson.index')->with('month', $month);
    }

    /**
     * Regeneruje planowane lekcje ucznia za dany miesiąc.
     * Zachowuje lekcje odbyte i odwołane (ręcznie edytowane) — kasuje tylko planowane.
     */
    private function regenerateStudentLessons(Student $student, string $month): void
    {
        // Kasuj tylko lekcje ze statusem "planned" — zachowaj odbyte/odwołane
        Lesson::where('student_id', $student->id)
            ->where('month', $month)
            ->where('status', LessonStatus::Planned)
            ->delete();

        // Pobierz istniejące (odbyte/odwołane) daty, żeby ich nie duplikować
        $existingDates = Lesson::where('student_id', $student->id)
            ->where('month', $month)
            ->pluck('lesson_date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->all();

        $startDate = Carbon::parse($month . '-01');
        $endDate = $startDate->copy()->endOfMonth();

        foreach ($student->schedule as $dayName => $time) {
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                // englishDayOfWeek jest odporny na locale (zawsze angielskie nazwy)
                $isMatch = strtolower($currentDate->englishDayOfWeek) === strtolower($dayName);

                if ($isMatch && !in_array($currentDate->format('Y-m-d'), $existingDates, true)) {
                    Lesson::create([
                        'student_id' => $student->id,
                        'lesson_date' => $currentDate->format('Y-m-d'),
                        'time' => $time,
                        'status' => LessonStatus::Planned,
                        'month' => $month,
                    ]);
                }
                $currentDate->addDay();
            }
        }

        $this->paymentSync->syncMonth($student, $month);
    }
}
