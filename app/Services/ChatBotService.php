<?php

namespace App\Services;

use App\Enums\LessonStatus;
use App\Enums\PaymentStatus;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatBotService
{
    public function getStudents(): array
    {
        return $this->runSafely('pobieranie uczniów', function () {
            $students = Student::all()->map(fn($student) => [
                'id' => $student->id,
                'name' => $student->name,
                'age' => $student->age,
                'class_number' => $student->class_number,
                'profile' => $student->profile,
                'current_topic' => $student->current_topic,
            ]);

            $duplicateFirstNames = $students
                ->map(fn($s) => mb_strtolower(explode(' ', trim($s['name']))[0] ?? ''))
                ->filter()
                ->countBy()
                ->filter(fn($count) => $count > 1)
                ->keys()
                ->values();

            return [
                'success' => true,
                'students' => $students->toArray(),
                'count' => $students->count(),
                'duplicate_first_names' => $duplicateFirstNames->toArray(),
                'ambiguity_warning' => $duplicateFirstNames->isNotEmpty()
                    ? 'W bazie są uczniowie o tym samym imieniu (' . $duplicateFirstNames->implode(', ') . '). Jeśli użytkownik użył tylko takiego imienia bez nazwiska, ZAWSZE dopytaj którego ucznia ma na myśli, ZANIM wykonasz jakąkolwiek inną akcję.'
                    : null,
            ];
        });
    }

    public function getLessons(?int $studentId = null, ?string $date = null, ?string $status = null): array
    {
        return $this->runSafely('pobieranie lekcji', function () use ($studentId, $date, $status) {
            $query = Lesson::with('student');

            if ($studentId) {
                $query->where('student_id', $studentId);
            }
            if ($date) {
                $query->where('lesson_date', $date);
            }
            if ($status && $enum = LessonStatus::tryFrom($status)) {
                $query->where('status', $enum);
            }

            $lessons = $query->orderBy('lesson_date', 'desc')->get()->map(fn($lesson) => [
                'id' => $lesson->id,
                'student_id' => $lesson->student_id,
                'student_name' => $lesson->student->name ?? 'Nieznany',
                'date' => $lesson->lesson_date,
                'time' => $lesson->time,
                'status' => $lesson->status->value,
                'status_label' => $lesson->status->label(),
                'month' => $lesson->month,
            ]);

            return [
                'success' => true,
                'lessons' => $lessons->toArray(),
                'count' => $lessons->count(),
            ];
        });
    }

    public function getPayments(?int $studentId = null, ?string $month = null, ?string $status = null): array
    {
        return $this->runSafely('pobieranie płatności', function () use ($studentId, $month, $status) {
            $query = Payment::with('student');

            if ($studentId) {
                $query->where('student_id', $studentId);
            }
            if ($month) {
                $query->where('month', $month);
            }
            if ($status && $enum = PaymentStatus::tryFrom($status)) {
                $query->where('status', $enum);
            }

            $payments = $query->orderBy('month', 'desc')->get()->map(fn($payment) => [
                'id' => $payment->id,
                'student_id' => $payment->student_id,
                'student_name' => $payment->student->name ?? 'Nieznany',
                'amount' => $payment->amount,
                'month' => $payment->month,
                'status' => $payment->status->value,
                'status_label' => $payment->status->label(),
                'payment_date' => $payment->payment_date,
                'lesson_count' => $payment->lesson_count,
            ]);

            return [
                'success' => true,
                'payments' => $payments->toArray(),
                'count' => $payments->count(),
            ];
        });
    }

    public function updateLessonStatus(int $lessonId, string $status): array
    {
        return $this->runSafely('zmiana statusu lekcji', function () use ($lessonId, $status) {
            $enum = LessonStatus::tryFrom($status);
            if (!$enum) {
                return [
                    'success' => false,
                    'message' => 'Nieprawidłowy status. Dozwolone: ' . implode(', ', LessonStatus::values()),
                ];
            }

            $lesson = Lesson::find($lessonId);
            if (!$lesson) {
                return [
                    'success' => false,
                    'message' => "Nie znaleziono lekcji o ID: {$lessonId}",
                ];
            }

            $oldStatus = $lesson->status;
            $lesson->status = $enum;
            $lesson->save();

            Log::info('ChatBot: zmiana statusu lekcji', [
                'lesson_id' => $lessonId,
                'old_status' => $oldStatus->value,
                'new_status' => $enum->value,
                'student_id' => $lesson->student_id,
            ]);

            return [
                'success' => true,
                'message' => "Status lekcji zmieniony z '{$oldStatus->label()}' na '{$enum->label()}'",
                'lesson' => [
                    'id' => $lesson->id,
                    'student' => $lesson->student->name ?? 'Nieznany',
                    'date' => $lesson->lesson_date,
                    'time' => $lesson->time,
                    'status' => $enum->value,
                ],
            ];
        }, ['lesson_id' => $lessonId]);
    }

    public function markPaymentAsPaid(int $paymentId, ?string $paymentDate = null): array
    {
        return $this->runSafely('oznaczanie płatności', function () use ($paymentId, $paymentDate) {
            $payment = Payment::find($paymentId);
            if (!$payment) {
                return [
                    'success' => false,
                    'message' => "Nie znaleziono płatności o ID: {$paymentId}",
                ];
            }

            $payment->status = PaymentStatus::Paid;
            $payment->payment_date = $paymentDate ?? now()->format('Y-m-d');
            $payment->save();

            Log::info('ChatBot: płatność oznaczona jako opłacona', [
                'payment_id' => $paymentId,
                'student_id' => $payment->student_id,
                'payment_date' => $payment->payment_date,
            ]);

            return [
                'success' => true,
                'message' => 'Płatność oznaczona jako opłacona',
                'payment' => [
                    'id' => $payment->id,
                    'student' => $payment->student->name ?? 'Nieznany',
                    'amount' => $payment->amount . ' zł',
                    'month' => $payment->month,
                    'payment_date' => $payment->payment_date,
                    'status' => $payment->status->value,
                ],
            ];
        }, ['payment_id' => $paymentId]);
    }

    public function updateStudentNotes(int $studentId, string $notes): array
    {
        return $this->updateStudentField($studentId, 'notes', $notes, 'notatki');
    }

    public function updateStudentTopic(int $studentId, string $topic): array
    {
        return $this->updateStudentField($studentId, 'current_topic', $topic, 'obecny materiał');
    }

    public function updateStudentDescription(int $studentId, string $description): array
    {
        return $this->updateStudentField($studentId, 'description', $description, 'opis');
    }

    private function updateStudentField(int $studentId, string $field, string $value, string $friendlyName): array
    {
        return $this->runSafely("aktualizacja pola {$field}", function () use ($studentId, $field, $value, $friendlyName) {
            $student = Student::find($studentId);
            if (!$student) {
                return [
                    'success' => false,
                    'message' => "Nie znaleziono ucznia o ID: {$studentId}",
                ];
            }

            $student->{$field} = $value;
            $student->save();

            // Uwaga: nie logujemy treści pola (mogą być dane wrażliwe ucznia)
            Log::info('ChatBot: aktualizacja pola ucznia', [
                'student_id' => $studentId,
                'field' => $field,
                'length' => strlen($value),
            ]);

            return [
                'success' => true,
                'message' => "✅ Zaktualizowano {$friendlyName}",
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    $field => $student->{$field},
                ],
            ];
        }, ['student_id' => $studentId, 'field' => $field]);
    }

    /**
     * Wspólny wrapper try/catch — loguje kontekst i zwraca ustandaryzowany błąd.
     */
    private function runSafely(string $action, callable $fn, array $context = []): array
    {
        try {
            return $fn();
        } catch (Throwable $e) {
            Log::error("ChatBot: błąd — {$action}", array_merge($context, [
                'error' => $e->getMessage(),
            ]));

            return [
                'success' => false,
                'message' => "Wystąpił błąd podczas: {$action}",
            ];
        }
    }
}
