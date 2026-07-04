<x-layout>
    <div class="page-header flex flex-col">
        <h1 class="page-title">Lekcje - {{ $student->name }}</h1>
        <a href="{{ route('lesson.index') }}?month={{ request('month') }}" class="btn btn-primary">
            ← Powrót do listy
        </a>
    </div>

    <div class="cards-container">
        @foreach ($lessons as $lesson)
            <div class="lesson-card">
                <div class="card-body">
                    <div class="lesson-date">
                        {{ $lesson['date'] }} - {{ $lesson['time'] }}
                    </div>
                    <div class="lesson-status status-{{ $lesson['status']->value }}">
                        Status: {{ $lesson['status']->label() }}
                    </div>
                </div>
                <div class="card-actions">
                    <a href="{{ route('lesson.edit', $lesson['id']) }}" class="btn btn-primary">Edytuj</a>
                </div>
            </div>
        @endforeach
    </div>
</x-layout>
