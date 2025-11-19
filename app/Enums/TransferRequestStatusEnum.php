<?php

namespace App\Enums;

enum TransferRequestStatusEnum: string
{
    case OPEN = 'open';
    case PARTIAL = 'partial';
    case CLOSED = 'closed';

    public static function fromInt(int $value): self
    {
        return match ($value) {
            0 => self::OPEN,
            1 => self::PARTIAL,
            2 => self::CLOSED,
            default => throw new \ValueError("Invalid status value: $value"),
        };
    }

}
