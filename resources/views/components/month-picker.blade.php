@props(['action', 'selected' => null])
@php
    $year = $selected ? (int) substr($selected, 0, 4) : (int) now()->year;
@endphp
<form method="GET" action="{{ $action }}" class="filter-form month-picker">
    <select name="month" class="form-select" onchange="this.form.submit()">
        <option value="">Wybierz miesiąc</option>
        @for ($i = 1; $i <= 12; $i++)
            @php $value = $year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
            <option value="{{ $value }}" @selected($selected == $value)>
                {{ ucfirst(\Carbon\Carbon::create($year, $i, 1)->translatedFormat('F')) }} {{ $year }}
            </option>
        @endfor
    </select>
</form>
