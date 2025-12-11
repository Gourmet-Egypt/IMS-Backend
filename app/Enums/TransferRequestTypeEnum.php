<?php

namespace App\Enums;

enum TransferRequestTypeEnum: string
{
    case PO = 'PO';
    case TransferIN = 'TransferIN';
    case TransferOut = 'TransferOut';
    case ReturnToSupplier = 'ReturnToSupplier';

    public function number(): int|string|array
    {
        return match ($this) {
            self::TransferIN => 2,
            self::TransferOut => 3,
            default => [],
        };
    }

}
