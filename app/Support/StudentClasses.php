<?php

namespace App\Support;

class StudentClasses
{
    public const CLASSES = [
        '6' => ['min' => 12, 'max' => 13],
        '7' => ['min' => 13, 'max' => 14],
        '8' => ['min' => 14, 'max' => 15],
        '1 LO' => ['min' => 15, 'max' => 16],
        '2 LO' => ['min' => 16, 'max' => 17],
        '3 LO' => ['min' => 17, 'max' => 18],
        '4 LO' => ['min' => 18, 'max' => 19],
    ];

    public static function labels(): array
    {
        return array_keys(self::CLASSES);
    }

    public static function ageRange(string $class): ?array
    {
        return self::CLASSES[$class] ?? null;
    }
}
