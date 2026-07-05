@use('App\Enums\PaymentStatus')
<x-layout>
    <div class="page-header">
        <h1 class="page-title">Edytuj płatność</h1>
        <a href="{{ route('payment.index') }}" class="btn btn-primary">
            ← Powrót do listy
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('payment.update', $payment) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Uczeń</label>
                <input type="text" class="form-input" value="{{ $payment->student->name }}" disabled>
            </div>

            <div class="form-group">
                <label for="month" class="form-label">Miesiąc</label>
                <input type="month" id="month" name="month" class="form-input" value="{{ $payment->month }}"
                    required>
            </div>

            <div class="form-group">
                <label for="lesson_count" class="form-label">Liczba lekcji</label>
                <input type="number" id="lesson_count" name="lesson_count" class="form-input"
                    value="{{ $payment->lesson_count }}" required>
            </div>

            <div class="form-group">
                <label for="amount" class="form-label">Kwota (zł)</label>
                <input type="number" id="amount" name="amount" class="form-input" step="0.01"
                    value="{{ $payment->amount }}" required>
            </div>

            <div class="form-group">
                <label for="payment_date" class="form-label">Data wpłaty</label>
                <input type="date" id="payment_date" name="payment_date" class="form-input"
                    value="{{ $payment->payment_date }}">
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" required>
                    @foreach (PaymentStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($payment->status === $status)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                <a href="{{ route('payment.index') }}" class="btn btn-danger">Anuluj</a>
            </div>
        </form>
    </div>

    <script>
        const price = {{ (float) ($payment->student->price_per_lesson ?? 0) }};
        const countEl = document.getElementById('lesson_count');
        const amountEl = document.getElementById('amount');
        countEl.addEventListener('input', () => {
            amountEl.value = (parseInt(countEl.value || 0, 10) * price).toFixed(2);
        });
    </script>
</x-layout>
