<x-layout>
    <div class="page-header">
        <h1 class="page-title">Szczegóły ucznia</h1>
        <a href="{{ route('student.index') }}" class="btn btn-primary">
            ← Powrót do listy
        </a>
    </div>

    <div class="detail-card">
        <div class="card-header">
            <h2 class="student-name">{{ $student->name }} ({{ $student->age }})</h2>
        </div>
        <div class="card-body">
            <div class="detail-row">
                <span class="detail-label">Numer klasy:</span>
                <span class="detail-value">{{ $student->class_number ?? '[Brak danych]' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Profil klasy:</span>
                <span class="detail-value">{{ $student->profile ?? '[Brak danych]' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Obecny temat:</span>
                <span class="detail-value">{{ $student->current_topic ?? '[Brak danych]' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Opis:</span>
                <span class="detail-value">{{ $student->description ?? '[Brak danych]' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Notatki:</span>
                <span class="detail-value">{{ $student->notes ?? '[Brak danych]' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Data sprawdzianu:</span>
                <span class="detail-value">{{ \App\Support\DateFormat::pl($student->next_exam_date) ?? '[Brak danych]' }}</span>
            </div>
        </div>
        <div class="buttons-wrap">
            <div class="card-actions">
                <a href="{{ route('student.edit', $student) }}" class="btn btn-primary">Edytuj</a>
            </div>
            <form method="POST" action="{{ route('student.destroy', $student) }}" style="display: inline;"
                onsubmit="return confirm('Czy na pewno chcesz usunąć tego ucznia?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Usuń</button>
            </form>
        </div>
    </div>
</x-layout>
