<?php

namespace App\Enums;

enum PurchaseOrderTypeEnum: int
{
    case LOCAL_PO_SUPPLIER_0 = 0;
    case LOCAL_PO_SUPPLIER_1 = 1;
    case TransferIN = 2;
    case TransferOut = 3;
    case TRANSFER_IN_HQ = 4;
    case TRANSFER_OUT_HQ = 5;

    /**
     * Map HQ transfer types to their standard transfer equivalents
     * for the external API: 4 (TRANSFER_IN_HQ) => 2 (TransferIN),
     * 5 (TRANSFER_OUT_HQ) => 3 (TransferOut). All other types pass through.
     */
    public function apiTransactionType(): self
    {
        return match ($this) {
            self::TRANSFER_IN_HQ => self::TransferIN,
            self::TRANSFER_OUT_HQ => self::TransferOut,
            default => $this,
        };
    }
}
