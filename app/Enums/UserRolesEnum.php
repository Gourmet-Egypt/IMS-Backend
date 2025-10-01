<?php

namespace App\Enums;

enum UserRolesEnum: string
{
    case Cashier = 'Cashier' ;

    case Daily = 'Daily';
    case Weekly = 'Weekly';
    case Monthly = 'Monthly';
    case Yearly = 'Yearly';
}
