<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderEntry extends Model
{

    protected $table = 'PurchaseOrderEntry';
    protected $hidden = ['DBTimeStamp'];


    protected $casts = [
        'LastUpdated' => 'datetime:Y-m-d',
    ];


    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'PurchaseOrderID', 'ID');
    }

    public function transferRequest()
    {
        return $this->belongsTo(TransferRequest::class, 'PurchaseOrderID', 'purchase_order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'ItemID', 'HQID');
    }

    public function HQ_item()
    {
        return $this->belongsTo(Item::class, 'ItemID', 'ID');
    }

    public function infos()
    {
        return $this->hasMany(TransferredItemInfo::class, 'purchase_order_entry_id', 'ID');
    }

    public function getTotalCostAttribute()
    {
        return $this->entries->sum(function ($entry) {
            return $entry->HQItem->Cost * $entry->QuantityOrdered;
        });
    }

    public function scopeEntryDetails($query, $id)
    {
        return $query->with([
            'infos',
            'HQ_item.category',
            'HQ_item.department'
        ])->where([
            ['ID', $id],
            ['StoreID', request()->input('store_id')]
        ]);
    }


}
