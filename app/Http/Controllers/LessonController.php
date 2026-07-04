<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Student;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LessonController extends Controller
{
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
                'completed' => $lessons->where('status', 'completed')->count(),
                'cancelled' => $lessons->where('status', 'canceled')->count(),
                'planned' => $lessons->where('status', 'planned')->count(),
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
                    'date' => Carbon::parse($lesson->lesson_date)->format('d.m.Y'),
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

        $lesson->update([
            'lesson_date' => $request->lesson_date,
            'time' => $request->time,
            'status' => $request->status,
            'month' => Carbon::parse($request->lesson_date)->format('Y-m'),
        ]);

        $this->updatePaymentForLesson($lesson);

        return redirect()->route('lesson.show', $lesson->student_id)
            ->with('month', $lesson->month);
    }

    public function generate(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $students = Student::whereNotNull('schedule')->get();

        foreach ($students as $student) {
            if (empty($student->schedule)) continue;

            Lesson::where('student_id', $student->id)
                ->where('month', $month)
                ->delete();

            $lessonCount = 0;
            $startDate = Carbon::parse($month . '-01');
            $endDate = $startDate->copy()->endOfMonth();

            foreach ($student->schedule as $dayName => $time) {
                $currentDate = $startDate->copy();

                while ($currentDate <= $endDate) {
                    if (strtolower($currentDate->format('l')) == $dayName) {
                        Lesson::create([
                            'student_id' => $student->id,
                            'lesson_date' => $currentDate->format('Y-m-d'),
                            'time' => $time,
                            'status' => 'planned',
                            'month' => $month,
                        ]);
                        $lessonCount++;
                    }
                    $currentDate->addDay();
                }
            }

            $payment = Payment::where('student_id', $student->id)
                ->where('month', $month)
                ->first();

            $amount = $lessonCount * ($student->price_per_lesson ?? 0);

            if ($payment) {
                $payment->update([
                    'lesson_count' => $lessonCount,
                    'amount' => $amount,
                ]);
            } else {
                Payment::create([
                    'student_id' => $student->id,
                    'month' => $month,
                    'lesson_count' => $lessonCount,
                    'amount' => $amount,
                    'status' => 'waiting',
                ]);
            }
        }

        return redirect()->route('lesson.index')->with('month', $month);
    }

    private function updatePaymentForLesson($lesson)
    {
        $payment = Payment::where('student_id', $lesson->student_id)
            ->where('month', $lesson->month)
            ->first();

        if ($payment) {
            $activeLessons = Lesson::where('student_id', $lesson->student_id)
                ->where('month', $lesson->month)
                ->where('status', '!=', 'canceled')
                ->count();

            $student = Student::find($lesson->student_id);
            $payment->update([
                'lesson_count' => $activeLessons,
                'amount' => $activeLessons * ($student->price_per_lesson ?? 0),
            ]);
        }
    }

    public function create()
    {
        return view('lesson.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}