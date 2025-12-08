<?php

namespace App\Models;

use App\Enums\TransferRequestStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransferRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function conditions(): HasOne
    {
        return $this->hasOne(TransferCondition::class, 'purchase_order_id', 'purchase_order_id');
    }

    public function itemTransfers(): HasMany
    {
        return $this->hasMany(TransferRequestItem::class, 'transfer_request_id', 'id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'transfer_request_item', 'transfer_request_id', 'item_id')
            ->using(TransferRequestItem::class)
            ->withPivot(['quantity', 'id', 'notes'])
            ->withTimestamps();
    }


    public function otherStore()
    {
        return $this->belongsTo(Store::class, 'other_store_id', 'StoreCode');
    }

    public function currentStore()
    {
        return $this->belongsTo(Store::class, 'store_id', 'StoreCode');
    }


    public function scopeSearch(Builder $query, $id)
    {
        return $query->where(
            ['id' => $id],
            ['status', TransferRequestStatusEnum::OPEN]
        );
    }


}
