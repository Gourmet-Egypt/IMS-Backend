<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'PurchaseOrder';


    public function scopeStore(Builder $query , $store_id)
    {
        return $query->where(['StoreID' => $store_id ,'POType' => 2 ]);
    }
}
