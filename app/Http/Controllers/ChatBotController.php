<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use App\Services\ChatBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotController extends Controller
{
    protected $chatBotService;

    public function __construct(ChatBotService $chatBotService)
    {
        $this->chatBotService = $chatBotService;
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'conversationHistory' => 'array'
        ]);

        $userMessage = $request->input('message');
        $conversationHistory = $request->input('conversationHistory', []);

        // Dodaj wiadomość użytkownika do historii
        $conversationHistory[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        try {
            $data = $this->callOpenAI($conversationHistory);
            $message = $data['choices'][0]['message'];

            // Sprawdź czy model chce wywołać funkcję
            if (isset($message['tool_calls']) && count($message['tool_calls']) > 0) {
                return $this->handleFunctionCalls($message, $conversationHistory);
            }

            // Zwykła odpowiedź tekstowa
            return response()->json([
                'response' => $message['content'],
                'conversationHistory' => array_merge($conversationHistory, [$message])
            ]);

        } catch (\Exception $e) {
            Log::error('ChatBot error: ' . $e->getMessage());

            return response()->json([
                'response' => 'Wystąpił błąd podczas przetwarzania zapytania.'
            ], 500);
        }
    }

    /**
     * Obsługa wywołania funkcji przez GPT (może być wiele wywołań)
     */
    protected function handleFunctionCalls($message, $conversationHistory)
    {
        // Dodaj wiadomość asystenta z tool_calls do historii
        $conversationHistory[] = $message;

        // Wykonaj wszystkie wywołania funkcji
        foreach ($message['tool_calls'] as $toolCall) {
            $functionName = $toolCall['function']['name'];
            $arguments = json_decode($toolCall['function']['arguments'], true);

            Log::info("ChatBot wywołuje funkcję: {$functionName}", $arguments);

            // Wykonaj funkcję
            $result = $this->executeFunction($functionName, $arguments);

            // Dodaj wynik funkcji do historii
            $conversationHistory[] = [
                'role' => 'tool',
                'tool_call_id' => $toolCall['id'],
                'content' => json_encode($result, JSON_UNESCAPED_UNICODE)
            ];
        }

        try {
            $data = $this->callOpenAI($conversationHistory);
            $finalMessage = $data['choices'][0]['message'];

            // Sprawdź czy model chce wywołać kolejne funkcje
            if (isset($finalMessage['tool_calls']) && count($finalMessage['tool_calls']) > 0) {
                return $this->handleFunctionCalls($finalMessage, $conversationHistory);
            }

            return response()->json([
                'response' => $finalMessage['content'],
                'conversationHistory' => array_merge($conversationHistory, [$finalMessage])
            ]);

        } catch (\Exception $e) {
            Log::error('ChatBot function call error: ' . $e->getMessage());
        }

        // Fallback
        return response()->json([
            'response' => 'Wykonano operację',
            'conversationHistory' => $conversationHistory
        ]);
    }

    /**
     * Wywołanie OpenAI API
     */
    private function callOpenAI(array $conversationHistory): array
    {
        $functions = require app_path('Services/functions.php');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => array_merge([
                [
                    'role' => 'system',
                    'content' => $this->getSystemPrompt()
                ]
            ], $conversationHistory),
            'tools' => $functions,
            'tool_choice' => 'auto',
            'max_tokens' => 1500,
            'temperature' => 0.7,
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API error: ' . $response->status());
        }

        return $response->json();
    }

    /**
     * Wykonaj funkcję na podstawie nazwy
     */
    protected function executeFunction(string $functionName, array $arguments): array
    {
        try {
            switch ($functionName) {
                case 'getStudents':
                    return $this->chatBotService->getStudents();

                case 'getLessons':
                    return $this->chatBotService->getLessons(
                        $arguments['studentId'] ?? null,
                        $arguments['date'] ?? null,
                        $arguments['status'] ?? null
                    );

                case 'getPayments':
                    return $this->chatBotService->getPayments(
                        $arguments['studentId'] ?? null,
                        $arguments['month'] ?? null,
                        $arguments['status'] ?? null
                    );

                case 'updateLessonStatus':
                    return $this->chatBotService->updateLessonStatus(
                        $arguments['lessonId'],
                        $arguments['status']
                    );

                case 'markPaymentAsPaid':
                    return $this->chatBotService->markPaymentAsPaid(
                        $arguments['paymentId'],
                        $arguments['paymentDate'] ?? null
                    );

                case 'updateStudentNotes':
                    return $this->chatBotService->updateStudentNotes(
                        $arguments['studentId'],
                        $arguments['notes']
                    );

                case 'updateStudentTopic':
                    return $this->chatBotService->updateStudentTopic(
                        $arguments['studentId'],
                        $arguments['topic']
                    );

                case 'updateStudentDescription':
                    return $this->chatBotService->updateStudentDescription(
                        $arguments['studentId'],
                        $arguments['description']
                    );

                default:
                    return [
                        'success' => false,
                        'message' => "Nieznana funkcja: {$functionName}"
                    ];
            }
        } catch (\Exception $e) {
            Log::error("Błąd wykonania funkcji {$functionName}: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas wykonywania operacji'
            ];
        }
    }

    /**
     * Pobierz system prompt z bazy danych
     */
    protected function getSystemPrompt(): string
    {
        $prompt = Prompt::first();

        return $prompt?->content ?? '';
    }
}