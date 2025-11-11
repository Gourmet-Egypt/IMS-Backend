<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderEntry extends Model
{
    use HasFactory;

    protected $table = 'PurchaseOrderEntry';

    protected $hidden = ['DBTimeStamp'];

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
        return $this->belongsTo(Item::class , 'ItemID' , 'HQID');
    }
    public function HQItem()
    {
        return $this->belongsTo(HQItem::class , 'ItemID' , 'HQID');
    }


    public function infos()
    {
        return $this->hasMany(TransferredItemInfo::class , 'purchase_order_entry_id' , 'ID');
    }

    public function getTotalCostAttribute()
    {
        return $this->entries->sum(function ($entry) {
            return $entry->HQItem->Cost * $entry->QuantityOrdered;
        });
    }

}
