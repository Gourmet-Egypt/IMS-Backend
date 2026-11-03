<?php

namespace App\Enums;

enum TransferRequestTypeEnum: string
{
    case PO = 'PO';
    case TransferIN = 'TransferIN';
    case TransferOut = 'TransferOut';
    case ReturnToSupplier = 'ReturnToSupplier';

    /**
     * POType value for this request type. Return to Supplier shares 3 with
     * Transfer Out (told apart by SupplierID vs OtherStoreID).
     */
    public function number(): int
    {
        return match ($this) {
            self::PO => PurchaseOrderTypeEnum::PO->value,
            self::TransferIN => PurchaseOrderTypeEnum::TransferIN->value,
            self::TransferOut,
            self::ReturnToSupplier => PurchaseOrderTypeEnum::TransferOut->value,
        };
    }
}
