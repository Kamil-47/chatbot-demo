<x-layout>
    <div class="page-header">
        <h1 class="page-title">Lekcje</h1>
        <div class="header-actions">
            <form method="GET" action="{{ route('lesson.index') }}" class="filter-form month-picker">
                <select name="month" class="form-select" onchange="this.form.submit()">
                    <option value="">Wybierz miesiąc</option>
                    @for ($i = 1; $i <= 12; $i++)
                        @php $value = '2026-' . str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $value }}" {{ $month == $value ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }} 2026
                        </option>
                    @endfor
                </select>
            </form>
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
                        <td>{{ $student['name'] }}</td>
                        <td>{{ $student['total'] }}</td>
                        <td>{{ $student['completed'] }}</td>
                        <td>{{ $student['cancelled'] }}</td>
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
