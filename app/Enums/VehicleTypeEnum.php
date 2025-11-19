<?php

namespace App\Enums;

enum VehicleTypeEnum: int
{
    case TRUCK = 1;
    case VAN = 2;
    case BIKE = 3;

    public static function keys(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function casesWithKeys(): array
    {
        return array_combine(
            array_map(fn($case) => $case->name, self::cases()),
            array_map(fn($case) => $case->value, self::cases())
        );
    }
}
