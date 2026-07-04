<x-layout>
    <div class="page-header">
        <h1 class="page-title">Dodaj nowego ucznia</h1>
        <a href="{{ route('student.index') }}" class="btn btn-primary">
            ← Powrót do listy
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('student.store') }}">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Imię i nazwisko</label>
                <input type="text" id="name" name="name" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="age" class="form-label">Wiek</label>
                <input type="number" id="age" name="age" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="class_number" class="form-label">Numer klasy</label>
                <input type="text" id="class_number" name="class_number" class="form-input">
            </div>

            <div class="form-group">
                <label for="profile" class="form-label">Profil klasy</label>
                <input type="text" id="profile" name="profile" class="form-input">
            </div>

            <div class="form-group">
                <label for="current_topic" class="form-label">Obecny temat</label>
                <input type="text" id="current_topic" name="current_topic" class="form-input">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Opis</label>
                <textarea id="description" name="description" class="form-textarea" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">Notatki</label>
                <textarea id="notes" name="notes" class="form-textarea" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="next_exam_date" class="form-label">Data sprawdzianu</label>
                <input type="date" id="next_exam_date" name="next_exam_date" class="form-input">
            </div>

            <div class="form-section">
                <h3 class="form-section-title">Harmonogram lekcji</h3>

                <div class="form-group">
                    <label class="form-label">Dni i godziny lekcji</label>
                    <div class="schedule-group">
                        <div class="schedule-row">
                            <label class="schedule-label">
                                <input type="checkbox" name="schedule_days[]" value="monday" class="schedule-checkbox">
                                <span>Poniedziałek</span>
                            </label>
                            <input type="time" name="schedule[monday]" class="form-input schedule-time">
                        </div>
                        <div class="schedule-row">
                            <label class="schedule-label">
                                <input type="checkbox" name="schedule_days[]" value="tuesday" class="schedule-checkbox">
                                <span>Wtorek</span>
                            </label>
                            <input type="time" name="schedule[tuesday]" class="form-input schedule-time">
                        </div>
                        <div class="schedule-row">
                            <label class="schedule-label">
                                <input type="checkbox" name="schedule_days[]" value="wednesday"
                                    class="schedule-checkbox">
                                <span>Środa</span>
                            </label>
                            <input type="time" name="schedule[wednesday]" class="form-input schedule-time">
                        </div>
                        <div class="schedule-row">
                            <label class="schedule-label">
                                <input type="checkbox" name="schedule_days[]" value="thursday"
                                    class="schedule-checkbox">
                                <span>Czwartek</span>
                            </label>
                            <input type="time" name="schedule[thursday]" class="form-input schedule-time">
                        </div>
                        <div class="schedule-row">
                            <label class="schedule-label">
                                <input type="checkbox" name="schedule_days[]" value="friday"
                                    class="schedule-checkbox">
                                <span>Piątek</span>
                            </label>
                            <input type="time" name="schedule[friday]" class="form-input schedule-time">
                        </div>
                        <div class="schedule-row">
                            <label class="schedule-label">
                                <input type="checkbox" name="schedule_days[]" value="saturday"
                                    class="schedule-checkbox">
                                <span>Sobota</span>
                            </label>
                            <input type="time" name="schedule[saturday]" class="form-input schedule-time">
                        </div>
                        <div class="schedule-row">
                            <label class="schedule-label">
                                <input type="checkbox" name="schedule_days[]" value="sunday"
                                    class="schedule-checkbox">
                                <span>Niedziela</span>
                            </label>
                            <input type="time" name="schedule[sunday]" class="form-input schedule-time">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="price_per_lesson" class="form-label">Cena za lekcję (zł)</label>
                    <input type="number" id="price_per_lesson" name="price_per_lesson" class="form-input"
                        step="0.01">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Zapisz</button>
                <a href="{{ route('student.index') }}" class="btn btn-danger">Anuluj</a>
            </div>
        </form>
    </div>
</x-layout>
