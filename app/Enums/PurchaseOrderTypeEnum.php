<?php

namespace App\Enums;

enum PurchaseOrderTypeEnum: int
{
    case PO = 1;
    case TRANSFER_IN = 2;
    case TRANSFER_OUT = 3;
    case RETURN_TO_SUPPLIER = 4;

    public function label(): string
    {
        return match($this) {
            self::PO => 'PO',
            self::TRANSFER_OUT => 'TransferOut',
            self::TRANSFER_IN => 'TransferIN',
            self::RETURN_TO_SUPPLIER => 'ReturnToSupplier',
        };
    }

    public static function fromValue(int $value): ?self
    {
        return match($value) {
            1 => self::PO,
            2 => self::TRANSFER_IN,
            3 => self::TRANSFER_OUT,
            4 => self::RETURN_TO_SUPPLIER,
            default => null,
        };
    }
}
