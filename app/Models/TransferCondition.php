<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferCondition extends Model
{

    protected $hidden = ['DBTimeStamp'];

    protected $table = 'IMS_PurchaseOrder_Conditions';

    protected $guarded = [];

    public function goodsType()
    {
        return $this->belongsTo(GoodsType::class, 'Goods_type');
    }

    public function getItemTempAttribute(): ?string
    {
        return $this->goodsType?->temperature_label;
    }

}
