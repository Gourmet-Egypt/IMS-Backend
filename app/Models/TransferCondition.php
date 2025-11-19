<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferCondition extends Model
{

    protected $hidden = ['DBTimeStamp'];

    protected $table = 'IMS_PurchaseOrder_Conditions';

    protected $guarded = [];

}
