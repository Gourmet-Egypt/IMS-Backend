<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferredItemInfo extends Model
{
    use HasFactory;
    protected $table = 'transferred_item_info';
    protected $guarded = [] ;

    public function itemTransferRequest(): BelongsTo
    {
        return $this->belongsTo(TransferRequestItem::class);
    }
}
