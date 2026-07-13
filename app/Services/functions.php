<?php

/**
 * Definicje funkcji dla OpenAI Function Calling
 * Te funkcje są wysyłane do API OpenAI aby model wiedział jakie akcje może wykonywać
 */

return [
    // =====================
    // FUNKCJE DO POBIERANIA DANYCH
    // =====================
    
    [
        'type' => 'function',
        'function' => [
            'name' => 'getStudents',
            'description' => 'Pobierz listę wszystkich uczniów. Dla każdego ucznia zwraca: id, name (imię i nazwisko razem), age, class_number, profile, current_topic. Uwaga: notes i description NIE są zwracane. Użyj tej funkcji gdy użytkownik pyta o uczniów lub wymienia imię ucznia.',
            'parameters' => [
                'type' => 'object',
                'properties' => (object)[],
                'required' => [],
            ]
        ]
    ],
    
    [
        'type' => 'function',
        'function' => [
            'name' => 'getLessons',
            'description' => 'Pobierz listę lekcji. Można filtrować po ID ucznia, dacie lub statusie. Użyj tej funkcji gdy użytkownik pyta o lekcje konkretnego ucznia lub o lekcje w określonym dniu.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'studentId' => [
                        'type' => 'integer',
                        'description' => 'ID ucznia (opcjonalne - jeśli podane, pokaż tylko lekcje tego ucznia)'
                    ],
                    'date' => [
                        'type' => 'string',
                        'description' => 'Data lekcji w formacie YYYY-MM-DD (opcjonalne)'
                    ],
                    'status' => [
                        'type' => 'string',
                        'enum' => ['planned', 'canceled', 'completed'],
                        'description' => 'Status lekcji (opcjonalne)'
                    ]
                ],
                'required' => [],
            ]
        ]
    ],
    
    [
        'type' => 'function',
        'function' => [
            'name' => 'getPayments',
            'description' => 'Pobierz listę płatności. Można filtrować po ID ucznia, miesiącu lub statusie. Użyj tej funkcji gdy użytkownik pyta kto zapłacił, kto nie zapłacił, lub o płatności konkretnego ucznia.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'studentId' => [
                        'type' => 'integer',
                        'description' => 'ID ucznia (opcjonalne - jeśli podane, pokaż tylko płatności tego ucznia)'
                    ],
                    'month' => [
                        'type' => 'string',
                        'description' => 'Miesiąc w formacie YYYY-MM (opcjonalne), np. "2025-01" dla stycznia 2025'
                    ],
                    'status' => [
                        'type' => 'string',
                        'enum' => ['waiting', 'paid'],
                        'description' => 'Status płatności (opcjonalne)'
                    ]
                ],
                'required' => [],
            ]
        ]
    ],

    // =====================
    // FUNKCJE DO MODYFIKACJI DANYCH
    // =====================
    
    [
        'type' => 'function',
        'function' => [
            'name' => 'updateLessonStatus',
            'description' => 'Zmień status lekcji (planned, canceled, completed). Użyj tej funkcji gdy użytkownik chce zmienić status lekcji, odwołać lekcję, oznaczyć jako odbytą itp.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'lessonId' => [
                        'type' => 'integer',
                        'description' => 'ID lekcji do zaktualizowania'
                    ],
                    'status' => [
                        'type' => 'string',
                        'enum' => ['planned', 'canceled', 'completed'],
                        'description' => 'Nowy status lekcji'
                    ]
                ],
                'required' => ['lessonId', 'status'],
            ]
        ]
    ],
    
    [
        'type' => 'function',
        'function' => [
            'name' => 'markPaymentAsPaid',
            'description' => 'Oznacz płatność jako opłaconą. Użyj tej funkcji gdy użytkownik informuje że uczeń zapłacił, wpłacił pieniądze, uregulował należność itp.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'paymentId' => [
                        'type' => 'integer',
                        'description' => 'ID płatności do oznaczenia jako opłacona'
                    ],
                    'paymentDate' => [
                        'type' => 'string',
                        'description' => 'Data wpłaty w formacie YYYY-MM-DD (opcjonalne, domyślnie dzisiaj)'
                    ]
                ],
                'required' => ['paymentId'],
            ]
        ]
    ],
    
    [
        'type' => 'function',
        'function' => [
            'name' => 'updateStudentNotes',
            'description' => 'Zaktualizuj notatki dotyczące ucznia. Użyj tej funkcji gdy użytkownik chce dodać lub zmienić notatki o uczniu.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'studentId' => [
                        'type' => 'integer',
                        'description' => 'ID ucznia'
                    ],
                    'notes' => [
                        'type' => 'string',
                        'description' => 'Nowe notatki dotyczące ucznia'
                    ]
                ],
                'required' => ['studentId', 'notes'],
            ]
        ]
    ],
    
    [
        'type' => 'function',
        'function' => [
            'name' => 'updateStudentTopic',
            'description' => 'Zaktualizuj obecny materiał/temat który realizuje uczeń. Użyj tej funkcji gdy użytkownik informuje o zmianie tematu lekcji, przejściu do nowego działu itp.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'studentId' => [
                        'type' => 'integer',
                        'description' => 'ID ucznia'
                    ],
                    'topic' => [
                        'type' => 'string',
                        'description' => 'Nowy temat/materiał'
                    ]
                ],
                'required' => ['studentId', 'topic'],
            ]
        ]
    ],
    
    [
        'type' => 'function',
        'function' => [
            'name' => 'updateStudentDescription',
            'description' => 'Zaktualizuj opis ucznia. Użyj tej funkcji gdy użytkownik chce zmienić ogólny opis ucznia, dodać informacje o jego charakterze, sposobie nauki itp.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'studentId' => [
                        'type' => 'integer',
                        'description' => 'ID ucznia'
                    ],
                    'description' => [
                        'type' => 'string',
                        'description' => 'Nowy opis ucznia'
                    ]
                ],
                'required' => ['studentId', 'description'],
            ]
        ]
    ],

    [
        'type' => 'function',
        'function' => [
            'name' => 'updateStudentExamDate',
            'description' => 'Ustaw datę najbliższego sprawdzianu/kartkówki/egzaminu ucznia. Użyj gdy użytkownik informuje o zbliżającym się sprawdzianie, zmianie terminu egzaminu itp.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'studentId' => [
                        'type' => 'integer',
                        'description' => 'ID ucznia'
                    ],
                    'examDate' => [
                        'type' => 'string',
                        'description' => 'Data sprawdzianu w formacie YYYY-MM-DD'
                    ]
                ],
                'required' => ['studentId', 'examDate'],
            ]
        ]
    ],

    [
        'type' => 'function',
        'function' => [
            'name' => 'rescheduleLesson',
            'description' => 'Przełóż istniejącą, zaplanowaną lekcję na inną datę i godzinę. Użyj gdy użytkownik informuje że lekcja została przełożona/przesunięta na inny termin. UWAGA: działa tylko na lekcjach ze statusem "planned". Aby zidentyfikować lekcję, najpierw wywołaj getLessons z filtrem po studentId i date (starej dacie), żeby wyciągnąć jej ID. Jeśli nowa godzina jest już zajęta przez inną nie-odwołaną lekcję, funkcja zwróci błąd z imieniem ucznia zajmującego slot — poproś wtedy użytkownika o inną datę lub godzinę.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'lessonId' => [
                        'type' => 'integer',
                        'description' => 'ID lekcji do przełożenia'
                    ],
                    'newDate' => [
                        'type' => 'string',
                        'description' => 'Nowa data lekcji w formacie YYYY-MM-DD'
                    ],
                    'newTime' => [
                        'type' => 'string',
                        'description' => 'Nowa godzina lekcji w formacie HH:MM (24h)'
                    ]
                ],
                'required' => ['lessonId', 'newDate', 'newTime'],
            ]
        ]
    ]
];