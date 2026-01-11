<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderEmail extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_rms';

//    protected $table = 'IMS_Purchase_order_emails';

    protected $guarded = [];
}
