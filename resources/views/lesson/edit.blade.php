@use('App\Enums\LessonStatus')
<x-layout>
    <div class="page-header">
        <h1 class="page-title">Edytuj lekcję</h1>
        <a href="{{ route('lesson.show', $lesson->student_id) }}?month={{ $lesson->month }}" class="btn btn-primary">
            ← Powrót
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('lesson.update', $lesson) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Uczeń</label>
                <input type="text" class="form-input" value="{{ $lesson->student->name }}" disabled>
            </div>

            <div class="form-group">
                <label for="lesson_date" class="form-label">Data lekcji</label>
                <input type="date" id="lesson_date" name="lesson_date" class="form-input"
                    value="{{ $lesson->lesson_date }}" required>
            </div>

            <div class="form-group">
                <label for="time" class="form-label">Godzina</label>
                <input type="time" id="time" name="time" class="form-input" value="{{ $lesson->time }}"
                    required>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" required>
                    @foreach (LessonStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($lesson->status === $status)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                <a href="{{ route('lesson.show', $lesson->student_id) }}?month={{ $lesson->month }}"
                    class="btn btn-danger">Anuluj</a>
            </div>
        </form>
    </div>
</x-layout>
