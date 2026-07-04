<x-layout>
    <div class="students-page">
        <div class="page-header">
            <h1 class="page-title">Uczniowie</h1>
            <div class="add-student">
                <a href="{{ route('student.create') }}" class="btn btn-primary">
                    + Dodaj ucznia
                </a>
            </div>
        </div>

        <div class="cards-container">
            @foreach ($students as $student)
                <div class="student-card">
                    <a href="{{ route('student.show', $student) }}" class="card-link">
                        <div class="card-header">
                            <h2 class="student-name">{{ $student->name }} ({{ $student->age }})</h2>
                        </div>

                        <div class="card-body">
                            <div class="student-info">
                                {{ $student->class_number ?? '[Brak klasy]' }} |
                                {{ $student->profile ?? '[Brak profilu]' }}
                            </div>
                            <div class="student-topic">
                                {{ $student->current_topic ?? '[Brak tematu]' }}
                            </div>
                        </div>
                        <div class="card-actions">
                            <a href="{{ route('student.show', $student) }}" class="btn btn-primary">Pokaż</a>
                            <a href="{{ route('student.edit', $student) }}" class="btn btn-primary">Edytuj</a>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</x-layout>
