@props(['action', 'selected' => null])
<form method="GET" action="{{ $action }}" class="filter-form month-picker">
    <select name="month" class="form-select" onchange="this.form.submit()">
        <option value="">Wybierz miesiąc</option>
        @for ($i = 1; $i <= 12; $i++)
            @php $value = '2026-' . str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
            <option value="{{ $value }}" @selected($selected == $value)>
                {{ ucfirst(\Carbon\Carbon::create(2026, $i, 1)->translatedFormat('F')) }} 2026
            </option>
        @endfor
    </select>
</form>
