<?php

namespace App\Enums;

enum PurchaseOrderTypeEnum: int
{
    case LOCAL_PO_SUPPLIER_0 = 0;
    case LOCAL_PO_SUPPLIER_1 = 1;
    case TRANSFER_IN = 2;
    case TRANSFER_OUT = 3;
    case TRANSFER_IN_HQ = 4;
    case TRANSFER_OUT_HQ = 5;

    public static function fromValue(int $value): ?self
    {
        return match ($value) {
            0 => self::LOCAL_PO_SUPPLIER_0,
            1 => self::LOCAL_PO_SUPPLIER_1,
            2 => self::TRANSFER_IN,
            3 => self::TRANSFER_OUT,
            4 => self::TRANSFER_IN_HQ,
            5 => self::TRANSFER_OUT_HQ,
            default => null,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::LOCAL_PO_SUPPLIER_0 => 'LocalPurchaseOrderSupplier(0)',
            self::LOCAL_PO_SUPPLIER_1 => 'LocalPurchaseOrderSupplier(1)',
            self::TRANSFER_IN => 'TransferIn',
            self::TRANSFER_OUT => 'TransferOut',
            self::TRANSFER_IN_HQ => 'TransferInFromHQ',
            self::TRANSFER_OUT_HQ => 'TransferOutFromHQ',
        };
    }
}
