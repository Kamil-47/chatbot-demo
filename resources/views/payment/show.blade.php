<x-layout>
    @use('App\Enums\PaymentStatus')
    <div class="page-header">
        <h1 class="page-title">Szczegóły płatności</h1>
        <a href="{{ route('payment.index') }}" class="btn btn-primary">
            ← Powrót do listy
        </a>
    </div>

    <div class="detail-card">
        <div class="card-header">
            <h2 class="student-name">{{ $paymentData['student_name'] }}</h2>
            <span class="payment-status status-{{ $paymentData['status']->value }}">
                {{ $paymentData['status']->label() }}
            </span>
        </div>
        <div class="card-body">
            <div class="detail-row">
                <span class="detail-label">Miesiąc:</span>
                <span class="detail-value">{{ $paymentData['formatted_month'] }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Liczba lekcji:</span>
                <span class="detail-value">{{ $paymentData['lesson_count'] }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Cena za lekcję:</span>
                <span class="detail-value">{{ $paymentData['student_price_per_lesson'] }}zł</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Kwota do zapłaty:</span>
                <span class="detail-value"><strong>{{ $paymentData['amount'] }}zł</strong></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Data wpłaty:</span>
                <span class="detail-value">{{ $paymentData['formatted_payment_date'] ?? 'Brak' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">{{ $paymentData['status']->label() }}</span>
            </div>
        </div>
        <div class="card-actions">
            @if ($paymentData['status'] === PaymentStatus::Waiting)
                <form method="POST" action="{{ route('payment.mark-paid', $paymentData['id']) }}"
                    style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">Oznacz jako opłacone</button>
                </form>
            @endif
            <a href="{{ route('payment.edit', $paymentData['id']) }}" class="btn btn-primary">Edytuj</a>
        </div>
    </div>
</x-layout>
