<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
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
        $schedule = [];
        if ($request->has('schedule')) {
            foreach ($request->schedule as $day => $time) {
                if ($request->has('schedule_days') && in_array($day, $request->schedule_days) && $time) {
                    $schedule[$day] = $time;
                }
            }
        }

        Student::create([
            'name' => $request->name,
            'age' => $request->age,
            'class_number' => $request->class_number,
            'profile' => $request->profile,
            'current_topic' => $request->current_topic,
            'description' => $request->description,
            'notes' => $request->notes,
            'next_exam_date' => $request->next_exam_date,
            'schedule' => $schedule,
            'price_per_lesson' => $request->price_per_lesson,
        ]);

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

        $schedule = [];
        if ($request->has('schedule')) {
            foreach ($request->schedule as $day => $time) {
                if ($request->has('schedule_days') && in_array($day, $request->schedule_days) && $time) {
                    $schedule[$day] = $time;
                }
            }
        }

        $student->update([
            'name' => $request->name,
            'age' => $request->age,
            'class_number' => $request->class_number,
            'profile' => $request->profile,
            'current_topic' => $request->current_topic,
            'description' => $request->description,
            'notes' => $request->notes,
            'next_exam_date' => $request->next_exam_date,
            'schedule' => $schedule,
            'price_per_lesson' => $request->price_per_lesson,
        ]);

        return redirect()->route('student.index');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('student.index');
    }
}