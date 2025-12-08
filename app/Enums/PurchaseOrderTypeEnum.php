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
}
