<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasStoreRelations;
use App\Models\Relations\HasOneInStore;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferredItemInfo extends Model
{
    use HasFactory, HasStoreRelations;

    public function purchaseOrder(): HasOneInStore
    {
        return $this->hasOneInStore(PurchaseOrder::class, 'ID', 'purchase_order_id');
    }

    public function entry(): HasOneInStore
    {
        return $this->hasOneInStore(PurchaseOrderEntry::class, 'ID', 'purchase_order_entry_id');
    }

    protected $table = 'IMS_PurchaseOrderEntry_infos';

    protected $hidden = [
        'DBTimeStamp'
    ];


    protected $casts = [
        'production_date' => 'datetime:Y-m-d',
        'expire_date' => 'datetime:Y-m-d',

    ];


    protected $guarded = [];

    public function itemTransferRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderEntry::class.'ItemID', 'purchase_order_entry_id');
    }
}
