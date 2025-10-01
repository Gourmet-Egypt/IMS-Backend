<?php

namespace App\Enums;

enum TransferRequestStatusEnum: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';

}
