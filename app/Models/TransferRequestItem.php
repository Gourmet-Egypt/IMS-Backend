<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;


class TransferRequestItem extends pivot
{
    use HasFactory;

    protected $table = 'transfer_request_item';

    public $timestamps = true;

    public function transferRequest(): BelongsTo
    {
        return $this->belongsTo(TransferRequest::class);
    }


    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'HQID');
    }


    public function itemInfos(): HasMany
    {
        return $this->hasMany(TransferredItemInfo::class , 'item_transfer_request_id' , 'id');
    }


    public function latestItemInfo(): HasOne
    {
        return $this->hasOne(TransferredItemInfo::class)->latest('created_at');
    }

    public function oldestItemInfo(): HasOne
    {
        return $this->hasOne(TransferredItemInfo::class)->oldest('production_date');
    }

    public function scopeByTransferRequest($query, $transferRequestId)
    {
        return $query->where('transfer_request_id', $transferRequestId);
    }

    public function scopeByItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

}
