<?php

namespace App\Enums;

enum ReasonEnum: string
{
    case SUPPLY_COST_EXCEPTION = 'supply_cost_exception';
    case RECEIVING_DIFFERENCE = 'receiving_difference';
    case WASTE_EXIT = 'waste_exit';
}
