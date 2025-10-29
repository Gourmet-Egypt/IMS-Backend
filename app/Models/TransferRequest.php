<?php

namespace App\Models;

use App\Enums\TransferRequestStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class TransferRequest extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    public function conditions(): HasOne
    {
        return $this->hasOne(TransferCondition::class);
    }

    public function itemTransfers(): HasMany
    {
        return $this->hasMany(TransferRequestItem::class , 'transfer_request_id' , 'id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'transfer_request_item', 'transfer_request_id', 'item_id')
            ->using(TransferRequestItem::class)
            ->withPivot(['quantity', 'id', 'notes'])
            ->withTimestamps();
    }


    public function transferToStore()
    {
        return $this->belongsTo(Store::class ,'to_store_id' , 'StoreCode' );
    }
    public function transferFromStore()
    {
        return $this->belongsTo(Store::class ,'from_store_id' , 'StoreCode' );
    }



    public function scopeSearch(Builder $query , $id)
    {
        return $query->where(
            ['id' => $id] ,
            ['status' , TransferRequestStatusEnum::OPEN]
        );
    }


}
