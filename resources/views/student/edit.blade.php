<x-layout>
    <div class="page-header">
        <h1 class="page-title">Edytuj ucznia</h1>
        <a href="{{ route('student.index') }}" class="btn btn-primary">
            ← Powrót do listy
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('student.update', $student) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="form-label">Imię i nazwisko</label>
                <input type="text" id="name" name="name" class="form-input @error('name') has-error @enderror" value="{{ old('name', $student->name) }}" required>
                @error('name')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="age" class="form-label">Wiek</label>
                <input type="number" id="age" name="age" class="form-input @error('age') has-error @enderror" value="{{ old('age', $student->age) }}" required>
                @error('age')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="class_number" class="form-label">Klasa</label>
                <input type="text" id="class_number" name="class_number" class="form-input @error('class_number') has-error @enderror" value="{{ old('class_number', $student->class_number) }}">
                @error('class_number')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="profile" class="form-label">Profil klasy</label>
                <input type="text" id="profile" name="profile" class="form-input @error('profile') has-error @enderror" value="{{ old('profile', $student->profile) }}">
                @error('profile')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="current_topic" class="form-label">Obecny temat</label>
                <input type="text" id="current_topic" name="current_topic" class="form-input @error('current_topic') has-error @enderror" value="{{ old('current_topic', $student->current_topic) }}">
                @error('current_topic')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Opis</label>
                <textarea id="description" name="description" class="form-textarea @error('description') has-error @enderror" rows="4">{{ old('description', $student->description) }}</textarea>
                @error('description')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">Notatki</label>
                <textarea id="notes" name="notes" class="form-textarea @error('notes') has-error @enderror" rows="4">{{ old('notes', $student->notes) }}</textarea>
                @error('notes')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="next_exam_date" class="form-label">Data sprawdzianu</label>
                <input type="date" id="next_exam_date" name="next_exam_date" class="form-input @error('next_exam_date') has-error @enderror" value="{{ old('next_exam_date', $student->next_exam_date) }}">
                @error('next_exam_date')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-section">
                <h3 class="form-section-title">Harmonogram lekcji</h3>

                <div class="form-group">
                    <label class="form-label">Dni i godziny lekcji</label>
                    <div class="schedule-group @error('schedule') has-error @enderror">
                        @php
                            $days = [
                                'monday' => 'Poniedziałek',
                                'tuesday' => 'Wtorek',
                                'wednesday' => 'Środa',
                                'thursday' => 'Czwartek',
                                'friday' => 'Piątek',
                                'saturday' => 'Sobota',
                                'sunday' => 'Niedziela',
                            ];
                            $studentSchedule = $student->schedule ?? [];
                            $oldDays = old('schedule_days', array_keys($studentSchedule));
                            $oldTimes = old('schedule', $studentSchedule);
                        @endphp

                        @foreach ($days as $key => $label)
                            <div class="schedule-row">
                                <label class="schedule-label">
                                    <input type="checkbox" name="schedule_days[]" value="{{ $key }}" class="schedule-checkbox" @checked(in_array($key, (array) $oldDays, true))>
                                    <span>{{ $label }}</span>
                                </label>
                                <input type="time" name="schedule[{{ $key }}]" class="form-input schedule-time" value="{{ $oldTimes[$key] ?? '' }}">
                            </div>
                        @endforeach
                    </div>
                    @error('schedule')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price_per_lesson" class="form-label">Cena za lekcję (zł)</label>
                    <input type="number" id="price_per_lesson" name="price_per_lesson" class="form-input @error('price_per_lesson') has-error @enderror" step="0.01" value="{{ old('price_per_lesson', $student->price_per_lesson) }}">
                    @error('price_per_lesson')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                <a href="{{ route('student.index') }}" class="btn btn-danger">Anuluj</a>
            </div>
        </form>
    </div>

    @if ($errors->has('schedule'))
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                alert("Zmiany nie zostały zapisane z powodu błędu w harmonogramie:\n\n" + @json($errors->first('schedule')));
            });
        </script>
    @endif
</x-layout>
