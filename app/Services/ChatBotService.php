<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class ChatBotService
{
    /**
     * Pobierz listę wszystkich uczniów
     * 
     * @return array
     */
    public function getStudents(): array
    {
        try {
            $students = Student::all()->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'age' => $student->age,
                    'class_number' => $student->class_number,
                    'profile' => $student->profile,
                    'current_topic' => $student->current_topic,
                ];
            });

            return [
                'success' => true,
                'students' => $students->toArray(),
                'count' => $students->count()
            ];

        } catch (\Exception $e) {
            Log::error("ChatBot: Błąd pobierania uczniów", [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => "Wystąpił błąd podczas pobierania listy uczniów"
            ];
        }
    }

    /**
     * Pobierz lekcje (opcjonalnie filtrowane po uczniu, dacie lub statusie)
     * 
     * @param int|null $studentId - ID ucznia (opcjonalne)
     * @param string|null $date - Data lekcji (opcjonalne)
     * @param string|null $status - Status (opcjonalne)
     * @return array
     */
    public function getLessons(?int $studentId = null, ?string $date = null, ?string $status = null): array
    {
        try {
            $query = Lesson::with('student');

            if ($studentId) {
                $query->where('student_id', $studentId);
            }

            if ($date) {
                $query->where('lesson_date', $date);
            }

            if ($status) {
                $query->where('status', $status);
            }

            $lessons = $query->orderBy('lesson_date', 'desc')->get()->map(function($lesson) {
                return [
                    'id' => $lesson->id,
                    'student_id' => $lesson->student_id,
                    'student_name' => $lesson->student->name ?? 'Nieznany',
                    'date' => $lesson->lesson_date,
                    'time' => $lesson->time,
                    'status' => $lesson->status,
                    'month' => $lesson->month
                ];
            });

            return [
                'success' => true,
                'lessons' => $lessons->toArray(),
                'count' => $lessons->count()
            ];

        } catch (\Exception $e) {
            Log::error("ChatBot: Błąd pobierania lekcji", [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => "Wystąpił błąd podczas pobierania lekcji"
            ];
        }
    }

    /**
     * Pobierz płatności (opcjonalnie filtrowane po uczniu, miesiącu lub statusie)
     * 
     * @param int|null $studentId - ID ucznia (opcjonalne)
     * @param string|null $month - Miesiąc w formacie YYYY-MM (opcjonalne)
     * @param string|null $status - Status płatności (opcjonalne)
     * @return array
     */
    public function getPayments(?int $studentId = null, ?string $month = null, ?string $status = null): array
    {
        try {
            $query = Payment::with('student');

            if ($studentId) {
                $query->where('student_id', $studentId);
            }

            if ($month) {
                $query->where('month', $month);
            }

            if ($status) {
                $query->where('status', $status);
            }

            $payments = $query->orderBy('month', 'desc')->get()->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'student_name' => $payment->student->name ?? 'Nieznany',
                    'amount' => $payment->amount,
                    'month' => $payment->month,
                    'status' => $payment->status,
                    'payment_date' => $payment->payment_date,
                    'lesson_count' => $payment->lesson_count
                ];
            });

            return [
                'success' => true,
                'payments' => $payments->toArray(),
                'count' => $payments->count()
            ];

        } catch (\Exception $e) {
            Log::error("ChatBot: Błąd pobierania płatności", [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => "Wystąpił błąd podczas pobierania płatności"
            ];
        }
    }

    /**
     * Zmień status lekcji
     * 
     * @param int $lessonId - ID lekcji
     * @param string $status - nowy status (planned/canceled/completed)
     * @return array
     */
    public function updateLessonStatus(int $lessonId, string $status): array
    {
        try {
            $lesson = Lesson::find($lessonId);
            
            if (!$lesson) {
                return [
                    'success' => false,
                    'message' => "Nie znaleziono lekcji o ID: {$lessonId}"
                ];
            }

            $allowedStatuses = ['planned', 'canceled', 'completed'];
            if (!in_array($status, $allowedStatuses)) {
                return [
                    'success' => false,
                    'message' => "Nieprawidłowy status. Dozwolone: " . implode(', ', $allowedStatuses)
                ];
            }

            $oldStatus = $lesson->status;
            $lesson->status = $status;
            $lesson->save();

            Log::info("ChatBot: Zmiana statusu lekcji", [
                'lesson_id' => $lessonId,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'student_id' => $lesson->student_id
            ]);

            return [
                'success' => true,
                'message' => "✅ Status lekcji zmieniony z '{$oldStatus}' na '{$status}'",
                'lesson' => [
                    'id' => $lesson->id,
                    'student' => $lesson->student->name ?? 'Nieznany',
                    'date' => $lesson->lesson_date,
                    'time' => $lesson->time,
                    'status' => $lesson->status
                ]
            ];

        } catch (\Exception $e) {
            Log::error("ChatBot: Błąd zmiany statusu lekcji", [
                'lesson_id' => $lessonId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => "Wystąpił błąd podczas zmiany statusu lekcji"
            ];
        }
    }

    /**
     * Oznacz płatność jako opłaconą
     * 
     * @param int $paymentId - ID płatności
     * @param string|null $paymentDate - data wpłaty (opcjonalnie)
     * @return array
     */
    public function markPaymentAsPaid(int $paymentId, ?string $paymentDate = null): array
    {
        try {
            $payment = Payment::find($paymentId);
            
            if (!$payment) {
                return [
                    'success' => false,
                    'message' => "Nie znaleziono płatności o ID: {$paymentId}"
                ];
            }

            $payment->status = 'paid';
            $payment->payment_date = $paymentDate ?? now()->format('Y-m-d');
            $payment->save();

            Log::info("ChatBot: Płatność oznaczona jako opłacona", [
                'payment_id' => $paymentId,
                'student_id' => $payment->student_id,
                'amount' => $payment->amount,
                'payment_date' => $payment->payment_date
            ]);

            return [
                'success' => true,
                'message' => "✅ Płatność oznaczona jako opłacona",
                'payment' => [
                    'id' => $payment->id,
                    'student' => $payment->student->name ?? 'Nieznany',
                    'amount' => $payment->amount . ' zł',
                    'month' => $payment->month,
                    'payment_date' => $payment->payment_date,
                    'status' => $payment->status
                ]
            ];

        } catch (\Exception $e) {
            Log::error("ChatBot: Błąd oznaczania płatności", [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => "Wystąpił błąd podczas oznaczania płatności"
            ];
        }
    }

    /**
     * Zaktualizuj notatki ucznia
     * 
     * @param int $studentId - ID ucznia
     * @param string $notes - nowe notatki
     * @return array
     */
    public function updateStudentNotes(int $studentId, string $notes): array
    {
        try {
            $student = Student::find($studentId);
            
            if (!$student) {
                return [
                    'success' => false,
                    'message' => "Nie znaleziono ucznia o ID: {$studentId}"
                ];
            }

            $student->notes = $notes;
            $student->save();

            Log::info("ChatBot: Aktualizacja notatek ucznia", [
                'student_id' => $studentId,
                'notes_length' => strlen($notes)
            ]);

            return [
                'success' => true,
                'message' => "✅ Notatki ucznia zaktualizowane",
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'notes' => $student->notes
                ]
            ];

        } catch (\Exception $e) {
            Log::error("ChatBot: Błąd aktualizacji notatek", [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => "Wystąpił błąd podczas aktualizacji notatek"
            ];
        }
    }

    /**
     * Zaktualizuj obecny materiał ucznia
     * 
     * @param int $studentId - ID ucznia
     * @param string $topic - nowy temat/materiał
     * @return array
     */
    public function updateStudentTopic(int $studentId, string $topic): array
    {
        try {
            $student = Student::find($studentId);
            
            if (!$student) {
                return [
                    'success' => false,
                    'message' => "Nie znaleziono ucznia o ID: {$studentId}"
                ];
            }

            $oldTopic = $student->current_topic;
            $student->current_topic = $topic;
            $student->save();

            Log::info("ChatBot: Aktualizacja materiału ucznia", [
                'student_id' => $studentId,
                'old_topic' => $oldTopic,
                'new_topic' => $topic
            ]);

            return [
                'success' => true,
                'message' => "✅ Obecny materiał zaktualizowany",
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'old_topic' => $oldTopic,
                    'current_topic' => $student->current_topic
                ]
            ];

        } catch (\Exception $e) {
            Log::error("ChatBot: Błąd aktualizacji materiału", [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => "Wystąpił błąd podczas aktualizacji materiału"
            ];
        }
    }

    /**
     * Zaktualizuj opis ucznia
     * 
     * @param int $studentId - ID ucznia
     * @param string $description - nowy opis
     * @return array
     */
    public function updateStudentDescription(int $studentId, string $description): array
    {
        try {
            $student = Student::find($studentId);
            
            if (!$student) {
                return [
                    'success' => false,
                    'message' => "Nie znaleziono ucznia o ID: {$studentId}"
                ];
            }

            $student->description = $description;
            $student->save();

            Log::info("ChatBot: Aktualizacja opisu ucznia", [
                'student_id' => $studentId,
                'description_length' => strlen($description)
            ]);

            return [
                'success' => true,
                'message' => "✅ Opis ucznia zaktualizowany",
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'description' => $student->description
                ]
            ];

        } catch (\Exception $e) {
            Log::error("ChatBot: Błąd aktualizacji opisu", [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => "Wystąpił błąd podczas aktualizacji opisu"
            ];
        }
    }
}