<?php

namespace App\Enums;

enum LessonStatus: string
{
    case Planned = 'planned';
    case Canceled = 'canceled';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Zaplanowana',
            self::Canceled => 'Odwołana',
            self::Completed => 'Odbyła się',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
