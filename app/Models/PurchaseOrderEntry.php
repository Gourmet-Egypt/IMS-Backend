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

    public function transferRequest()
    {
        return $this->belongsTo(TransferRequest::class , 'PurchaseOrderID' , 'purchase_order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class , 'ItemID' , 'ID');
    }


    public function infos()
    {
        return $this->hasMany(TransferredItemInfo::class , 'purchase_order_entry_id' , 'ID');
    }


}
