<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    private const WEEKDAYS = [
        'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday',
    ];

    private const WEEKDAY_LABELS = [
        'monday' => 'poniedziałek',
        'tuesday' => 'wtorek',
        'wednesday' => 'środa',
        'thursday' => 'czwartek',
        'friday' => 'piątek',
        'saturday' => 'sobota',
        'sunday' => 'niedziela',
    ];

    public function index()
    {
        $students = Student::all();
        return view('student.index', compact('students'));
    }

    public function create()
    {
        return view('student.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['schedule'] = $this->buildSchedule($request);

        $this->assertNoScheduleConflicts($data['schedule']);

        Student::create($data);

        return redirect()->route('student.index');
    }

    public function show($id)
    {
        $student = Student::findOrFail($id);
        return view('student.show', compact('student'));
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('student.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $data = $this->validated($request);
        $data['schedule'] = $this->buildSchedule($request);

        $this->assertNoScheduleConflicts($data['schedule'], $student->id);

        $student->update($data);

        return redirect()->route('student.index');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('student.index');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:150',
            'class_number' => 'nullable|string|max:50',
            'profile' => 'nullable|string|max:255',
            'current_topic' => 'nullable|string|max:2000',
            'description' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:5000',
            'next_exam_date' => 'nullable|date',
            'price_per_lesson' => 'nullable|numeric|min:0|max:99999.99',
        ]);
    }

    private function buildSchedule(Request $request): array
    {
        $schedule = [];
        $selectedDays = (array) $request->input('schedule_days', []);
        $times = (array) $request->input('schedule', []);

        foreach (self::WEEKDAYS as $day) {
            if (in_array($day, $selectedDays, true) && !empty($times[$day])) {
                $schedule[$day] = $times[$day];
            }
        }

        return $schedule;
    }

    private function assertNoScheduleConflicts(array $schedule, ?int $ignoreStudentId = null): void
    {
        if (empty($schedule)) {
            return;
        }

        $query = Student::query();
        if ($ignoreStudentId !== null) {
            $query->where('id', '!=', $ignoreStudentId);
        }

        $conflicts = [];
        foreach ($query->get() as $other) {
            $otherSchedule = $other->schedule ?? [];
            foreach ($schedule as $day => $time) {
                $otherTime = $otherSchedule[$day] ?? null;
                if ($otherTime === null) {
                    continue;
                }
                if ($this->normalizeTime($otherTime) === $this->normalizeTime($time)) {
                    $conflicts[] = sprintf(
                        '%s o %s jest już zajęty (%s).',
                        ucfirst(self::WEEKDAY_LABELS[$day] ?? $day),
                        $this->normalizeTime($time),
                        $other->name,
                    );
                }
            }
        }

        if (!empty($conflicts)) {
            throw ValidationException::withMessages([
                'schedule' => $conflicts,
            ]);
        }
    }

    private function normalizeTime(string $time): string
    {
        return substr($time, 0, 5);
    }
}
