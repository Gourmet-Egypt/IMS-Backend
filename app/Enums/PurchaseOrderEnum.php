<?php

namespace App\Enums;

enum PurchaseOrderEnum: string
{
    case MAIN = 'main';
    case INFO = 'info';
    case CONDITION = 'condition';
}
