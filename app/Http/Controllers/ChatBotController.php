<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use App\Services\ChatBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotController extends Controller
{
    // Maksymalna liczba iteracji pętli tool_calls w jednym requeście usera.
    // Chroni przed sytuacją gdy model wpada w pętlę wywołań funkcji.
    private const MAX_TOOL_CALL_ITERATIONS = 5;

    // Domyślny system prompt gdy brak rekordu w tabeli prompts.
    private const DEFAULT_SYSTEM_PROMPT = 'Jesteś asystentem korepetycji. Pomagasz zarządzać uczniami, lekcjami i płatnościami. Odpowiadaj krótko i konkretnie po polsku.';

    public function __construct(private ChatBotService $chatBotService)
    {
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'conversationHistory' => 'array',
        ]);

        $userMessage = $request->input('message');

        // Bierzemy tylko wiadomości user/assistant — role system/tool od klienta
        // to wektor prompt injection (klient mógłby podszyć się pod wynik funkcji).
        $conversationHistory = $this->sanitizeHistory(
            $request->input('conversationHistory', [])
        );

        $conversationHistory[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        try {
            for ($iteration = 0; $iteration < self::MAX_TOOL_CALL_ITERATIONS; $iteration++) {
                $data = $this->callOpenAI($conversationHistory);
                $message = $data['choices'][0]['message'];

                if (!isset($message['tool_calls']) || count($message['tool_calls']) === 0) {
                    return response()->json([
                        'response' => $message['content'],
                        'conversationHistory' => array_merge($conversationHistory, [$message]),
                    ]);
                }

                $conversationHistory[] = $message;
                foreach ($message['tool_calls'] as $toolCall) {
                    $conversationHistory[] = $this->executeToolCall($toolCall);
                }
            }

            Log::warning('ChatBot: przekroczono limit iteracji tool_calls');

            return response()->json([
                'response' => 'Operacja przekroczyła dopuszczalną liczbę kroków. Spróbuj przeformułować prośbę.',
                'conversationHistory' => $conversationHistory,
            ]);
        } catch (\Exception $e) {
            Log::error('ChatBot error: ' . $e->getMessage());

            return response()->json([
                'response' => 'Wystąpił błąd podczas przetwarzania zapytania.',
            ], 500);
        }
    }

    /**
     * Odrzuca wiadomości z rolami system/tool oraz assistant z tool_calls od klienta
     * i przycina zawartość do rozsądnych rozmiarów.
     */
    private function sanitizeHistory(array $history): array
    {
        $clean = [];
        foreach ($history as $msg) {
            if (!is_array($msg) || !isset($msg['role'])) {
                continue;
            }
            if ($msg['role'] !== 'user' && $msg['role'] !== 'assistant') {
                continue;
            }

            $content = isset($msg['content']) && is_string($msg['content'])
                ? mb_substr($msg['content'], 0, 4000)
                : '';

            // Pomijaj puste assistant messages — pochodzą z tur w których
            // model odpowiedział tylko tool_calls (content: null).
            if ($msg['role'] === 'assistant' && $content === '') {
                continue;
            }

            $clean[] = ['role' => $msg['role'], 'content' => $content];
        }
        return $clean;
    }

    private function executeToolCall(array $toolCall): array
    {
        $functionName = $toolCall['function']['name'];
        $arguments = json_decode($toolCall['function']['arguments'], true) ?? [];

        Log::info('ChatBot wywołuje funkcję', [
            'function' => $functionName,
            'argument_keys' => array_keys($arguments),
        ]);

        $result = $this->dispatchFunction($functionName, $arguments);

        return [
            'role' => 'tool',
            'tool_call_id' => $toolCall['id'],
            'content' => json_encode($result, JSON_UNESCAPED_UNICODE),
        ];
    }

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
                    'content' => $this->getSystemPrompt(),
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

    private function dispatchFunction(string $functionName, array $arguments): array
    {
        try {
            return match ($functionName) {
                'getStudents' => $this->chatBotService->getStudents(),
                'getLessons' => $this->chatBotService->getLessons(
                    $arguments['studentId'] ?? null,
                    $arguments['date'] ?? null,
                    $arguments['status'] ?? null,
                ),
                'getPayments' => $this->chatBotService->getPayments(
                    $arguments['studentId'] ?? null,
                    $arguments['month'] ?? null,
                    $arguments['status'] ?? null,
                ),
                'updateLessonStatus' => $this->chatBotService->updateLessonStatus(
                    $arguments['lessonId'],
                    $arguments['status'],
                ),
                'markPaymentAsPaid' => $this->chatBotService->markPaymentAsPaid(
                    $arguments['paymentId'],
                    $arguments['paymentDate'] ?? null,
                ),
                'updateStudentNotes' => $this->chatBotService->updateStudentNotes(
                    $arguments['studentId'],
                    $arguments['notes'],
                ),
                'updateStudentTopic' => $this->chatBotService->updateStudentTopic(
                    $arguments['studentId'],
                    $arguments['topic'],
                ),
                'updateStudentDescription' => $this->chatBotService->updateStudentDescription(
                    $arguments['studentId'],
                    $arguments['description'],
                ),
                default => [
                    'success' => false,
                    'message' => "Nieznana funkcja: {$functionName}",
                ],
            };
        } catch (\Throwable $e) {
            Log::error('Błąd wykonania funkcji', [
                'function' => $functionName,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas wykonywania operacji',
            ];
        }
    }

    private function getSystemPrompt(): string
    {
        $prompt = Prompt::first();
        $content = $prompt?->content ?? '';

        return trim($content) !== '' ? $content : self::DEFAULT_SYSTEM_PROMPT;
    }
}
