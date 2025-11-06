<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferCondition extends Model
{
    use HasFactory;

    protected $hidden = ['DBTimeStamp'];

    protected $table = 'IMS_PurchaseOrder_Conditions'  ;

    protected $guarded = [] ;


}
