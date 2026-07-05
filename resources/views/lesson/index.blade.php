<x-layout>
    <div class="page-header">
        <h1 class="page-title">Lekcje</h1>
        <div class="header-actions">
            <x-month-picker :action="route('lesson.index')" :selected="$month" />
            <form method="POST" action="{{ route('lesson.generate') }}" style="display: inline;"
                class="generate-lessons">
                @csrf
                <input type="hidden" name="month" value="{{ $month ?? now()->format('Y-m') }}">
                <button type="submit" class="btn btn-primary">Generuj lekcje</button>
            </form>
        </div>
    </div>

    <div class="table-container lesson-table">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Uczeń</th>
                    <th>Lekcji w miesiącu</th>
                    <th>Odbytych</th>
                    <th>Odwołanych</th>
                    <th>Zaplanowanych</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($studentsData as $student)
                    <tr>
                        <td><a href="{{ route('student.show', $student['id']) }}" class="student-link">{{ $student['name'] }}</a></td>
                        <td>{{ $student['total'] }}</td>
                        <td>{{ $student['completed'] }}</td>
                        <td>{{ $student['canceled'] }}</td>
                        <td>{{ $student['planned'] }}</td>
                        <td>
                            <a href="{{ route('lesson.show', $student['id']) }}?month={{ $month }}"
                                class="btn btn-primary">
                                Zobacz szczegóły
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layout>
