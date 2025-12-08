<?php

namespace App\Enums;

enum TransferRequestTypeEnum: string
{
    case PO = 'PO';
    case TransferIN = 'TransferIN';
    case TransferOut = 'TransferOut';
    case ReturnToSupplier = 'ReturnToSupplier';


}
