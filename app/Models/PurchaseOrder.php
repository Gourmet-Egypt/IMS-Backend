<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'PurchaseOrder';

    protected $hidden = ['DBTimeStamp'];

    public static function onSecondary()
    {
        return static::on('sqlsrv_rms');
    }



    public function scopeStore(Builder $query): Builder
    {
        $store_id = Auth::user()->store_id;
        $type = request('type');
        $status = request('status', 0);

        return $query->where([
            ['StoreID', $store_id],
            ['SupplierID', '=', 0],
            ['OtherStoreID', '<>', 0],
            ['Status', $status],
        ])->whereIn('POType', (array) $type);
    }


    public function entries()
    {
        return $this->hasMany(PurchaseOrderEntry::class , 'PurchaseOrderID' , 'ID');
    }

    public function currentStore()
    {
        return $this->belongsTo(Store::class ,'StoreID' , 'StoreCode' );
    }
    public function otherStore()
    {
        return $this->belongsTo(Store::class ,'OtherStoreID' , 'StoreCode' );
    }

    public function condition()
    {
        return $this->hasOne(TransferCondition::class, 'purchase_order_id' , 'ID');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class ,'Supplierid' , 'HQID' );
    }

    public function scopeType(Builder $query)
    {
        $type = request('type');

        return $type
            ? $query->whereIn('POType', (array)$type)
            : $query;
    }



    public function pdfs()
    {
        return $this->hasMany(PurchaseOrderPdf::class, 'purchase_order_id', 'ID');
    }

    public function emails()
    {
        return $this->hasMany(PurchaseOrderEmail::class, 'purchase_order_id', 'ID');
    }

    public function quantityMaxThousand($purchaseOrder)
    {
        $items = $purchaseOrder->whereHas('entries', function ($query) use ($purchaseOrder) {
            $query->where('QuantityOrdered' , '>' , 1000)->get() ;
        });

        return $items;
    }



}
