<?php

namespace App\Enums;

enum PurchaseOrderTypeEnum: int
{
    case PO = 0;
    case PO_HQ = 1;
    case TransferIN = 2;
    case TransferOut = 3;
    case TRANSFER_IN_HQ = 4;
    case TRANSFER_OUT_HQ = 5;

    /**
     * Map HQ types to their standard equivalents for the external API:
     * 1 (PO_HQ) => 0 (PO), 4 (TRANSFER_IN_HQ) => 2 (TransferIN),
     * 5 (TRANSFER_OUT_HQ) => 3 (TransferOut). All other types pass through.
     */
    public function apiTransactionType(): self
    {
        return match ($this) {
            self::PO_HQ => self::PO,
            self::TRANSFER_IN_HQ => self::TransferIN,
            self::TRANSFER_OUT_HQ => self::TransferOut,
            default => $this,
        };
    }

    /**
     * 3 / 5 are Return to Supplier with a supplier and no other store, Transfer Out
     * with another store and no supplier; null when the order has both or neither.
     */
    public function operation(int $supplierId, int $otherStoreId): ?string
    {
        return match ($this) {
            self::PO, self::PO_HQ => 'PO',
            self::TransferIN, self::TRANSFER_IN_HQ => 'TransferIN',
            self::TransferOut, self::TRANSFER_OUT_HQ => match (true) {
                $supplierId !== 0 && $otherStoreId === 0 => 'ReturnToSupplier',
                $otherStoreId !== 0 && $supplierId === 0 => 'TransferOut',
                default => null,
            },
        };
    }
}
