<?php

namespace App\Support;

use Carbon\Carbon;

class DateFormat
{
    public static function pl(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->format('d-m-Y');
    }
}
