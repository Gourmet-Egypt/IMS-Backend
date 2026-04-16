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

    /**
     * Get item by HQID (ItemID matches Item.HQID)
     * Note: PurchaseOrderEntry.ItemID stores Item.ID, so use itemById() instead
     */
    public function itemByHqid()
    {
        return $this->belongsTo(Item::class, 'ItemID', 'HQID');
    }

    /**
     * Get item by ID (ItemID matches Item.ID)
     * This is the correct relation for PurchaseOrderEntry
     */
    public function itemById()
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
            return $entry->itemById->Cost * $entry->QuantityOrdered;
        });
    }

    public function scopeEntryDetails($query, $id)
    {
        return $query->with([
            'infos',
            'itemById.category',
            'itemById.department'
        ])->where([
            ['ID', $id],
            ['StoreID', request()->input('store_id')]
        ]);
    }


}
