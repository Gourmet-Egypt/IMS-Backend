<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferredItemInfo extends Model
{
    use HasFactory;
    protected $table = 'purchase_order_entry_infos';
    protected $guarded = [] ;

    public function itemTransferRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderEntry::class . 'ItemID' , 'purchase_order_entry_id');
    }
}
