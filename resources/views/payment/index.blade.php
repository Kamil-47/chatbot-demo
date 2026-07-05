@use('App\Enums\PaymentStatus')
<x-layout>
    <div class="page-header">
        <h1 class="page-title">Płatności</h1>
        <div class="header-actions">
            <form method="GET" action="{{ route('payment.index') }}" class="filter-form month-picker">
                <select name="month" class="form-select" onchange="this.form.submit()">
                    <option value="">Wybierz miesiąc</option>
                    @for ($i = 1; $i <= 12; $i++)
                        @php $value = '2026-' . str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $value }}" {{ request('month') == $value ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }} 2026
                        </option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    <div class="cards-container">
        @foreach ($payments as $payment)
            <div class="payment-card {{ $payment['status']->cssClass() }}">
                <div class="card-header">
                    <h2 class="student-name">{{ $payment['student_name'] }}</h2>
                    <span class="payment-status status-{{ $payment['status']->value }}">
                        {{ $payment['status']->label() }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="payment-details">
                        <div class="payment-info">
                            {{ $payment['lesson_count'] }} lekcji × {{ $payment['student_price_per_lesson'] }}zł
                            = <strong>{{ $payment['amount'] }}zł</strong>
                        </div>
                        <div class="payment-month">
                            Miesiąc: {{ $payment['formatted_month'] }}
                        </div>
                        @if ($payment['formatted_payment_date'])
                            <div class="payment-date">
                                Data wpłaty: {{ $payment['formatted_payment_date'] }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-actions">
                    @if ($payment['status'] === PaymentStatus::Waiting)
                        <form method="POST" action="{{ route('payment.mark-paid', $payment['id']) }}"
                            style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary">Oznacz jako opłacone</button>
                        </form>
                    @endif
                    <a href="{{ route('payment.show', $payment['id']) }}" class="btn btn-primary">Pokaż</a>
                    <a href="{{ route('payment.edit', $payment['id']) }}" class="btn btn-primary">Edytuj</a>
                </div>
            </div>
        @endforeach
    </div>
</x-layout>
