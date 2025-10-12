<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderEntry extends Model
{
    use HasFactory;

    protected $table = 'PurchaseOrderEntry';

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class , 'PurchaseOrderID' , 'ID');
    }

    public function item()
    {
        return $this->belongsTo(Item::class , 'ItemID' , 'ID');
    }

    public function transferRequest()
    {
        return $this->belongsTo(
            TransferRequest::class,
            'PurchaseOrderID',
            'purchase_order_id'
        );
    }

}
