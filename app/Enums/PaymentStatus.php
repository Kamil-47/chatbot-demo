<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Waiting = 'waiting';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'Oczekująca',
            self::Paid => 'Opłacona',
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::Waiting => 'unpaid',
            self::Paid => 'paid',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
