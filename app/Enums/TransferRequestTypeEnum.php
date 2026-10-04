<?php

namespace App\Enums;

enum TransferRequestTypeEnum: string
{
    case PO = 'PO';
    case TransferIN = 'TransferIN';
    case TransferOut = 'TransferOut';
    case ReturnToSupplier = 'ReturnToSupplier';

    public function number(): int
    {
        return match ($this) {
            self::PO => 1,
            self::ReturnToSupplier => 0,
            self::TransferIN => 2,
            self::TransferOut => 3,
        };
    }

}
