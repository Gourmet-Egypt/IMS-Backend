<?php

namespace App\Models;

use App\Enums\PurchaseOrderTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'PurchaseOrder';

    protected $hidden = ['DBTimeStamp'];

    public static function storeReport()
    {
        $storeId = request()->store_id;
        $status = request()->status;
        $type = request()->type;

        $query = self::onSecondary()->where('StoreID', $storeId);


        if (!is_null($type) && !is_null($status)) {
            return [
                'type' => (int) $type,
                'status' => (int) $status,
                'count' => $query->where('POType', $type)
                    ->where('status', $status)
                    ->count(),
            ];
        }

        if (!is_null($type)) {
            $statusCounts = $query
                ->where('POType', $type)
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');

            return [
                'type' => (int) $type,
                'open' => $statusCounts->get(0, 0),
                'partial' => $statusCounts->get(1, 0),
                'closed' => $statusCounts->get(2, 0),
                'total' => $statusCounts->sum(),
            ];
        }


        if (!is_null($status)) {
            $typeCounts = $query
                ->where('status', $status)
                ->select('POType', DB::raw('COUNT(*) as count'))
                ->groupBy('POType')
                ->pluck('count', 'POType');

            return [
                'status' => (int) $status,
                'local_po_supplier_0' => $typeCounts->get(PurchaseOrderTypeEnum::LOCAL_PO_SUPPLIER_0->value, 0),
                'local_po_supplier_1' => $typeCounts->get(PurchaseOrderTypeEnum::LOCAL_PO_SUPPLIER_1->value, 0),
                'transfer_in' => $typeCounts->get(PurchaseOrderTypeEnum::TRANSFER_IN->value, 0),
                'transfer_out' => $typeCounts->get(PurchaseOrderTypeEnum::TRANSFER_OUT->value, 0),
                'transfer_in_hq' => $typeCounts->get(PurchaseOrderTypeEnum::TRANSFER_IN_HQ->value, 0),
                'transfer_out_hq' => $typeCounts->get(PurchaseOrderTypeEnum::TRANSFER_OUT_HQ->value, 0),
                'total' => $typeCounts->sum(),
            ];
        }

        // Optional: If only store_id (you can remove this if not needed)
        return [
            'error' => 'Please provide at least type or status parameter'
        ];
    }

    public static function onSecondary()
    {
        return static::on('sqlsrv_rms');
    }


    public function scopeTransferReports($query, $id)
    {
        $store_id = request()->get('store_id');

        return $query->with([
            'entries',
            'entries.infos',
            'condition',
            'entries.item',
            'entries.item.category',
            'entries.item.department'
        ])
            ->where([
                ['PONumber', $id],
                ['StoreID', $store_id]
            ])
            ->first();
    }

    public function scopeStore(Builder $query): Builder
    {
        $store_id = Auth::user()->store_id;
        $type = request('type');
        $status = request('status', 0);


        $new_query = $query->where([
            ['StoreID', $store_id],
            ['SupplierID', '=', 0],
            ['OtherStoreID', '<>', 0],
            ['Status', $status],
        ]);
        if (!$type) {
            return $new_query;
        } else {
            if (is_string($type)) {
                $type = json_decode($type, true) ?? explode(',', $type);
            }
            return $new_query->whereIn('POType', (array) $type);
        }
    }

    public function entries()
    {
        return $this->hasMany(PurchaseOrderEntry::class, 'PurchaseOrderID', 'ID');
    }

    public function currentStore()
    {
        return $this->belongsTo(Store::class, 'StoreID', 'StoreCode');
    }

    public function otherStore()
    {
        return $this->belongsTo(Store::class, 'OtherStoreID', 'StoreCode');
    }

    public function condition()
    {
        return $this->hasOne(TransferCondition::class, 'purchase_order_id', 'ID');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'Supplierid', 'HQID');
    }

    public function scopeType(Builder $query)
    {
        $type = request('type');

        if (!$type) {
            return $query;
        }

        // Convert string to array if needed
        if (is_string($type)) {
            $type = json_decode($type, true) ?? explode(',', $type);
        }

        return $query->whereIn('POType', (array) $type);
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
            $query->where('QuantityOrdered', '>', 1000)->get();
        });

        return $items;
    }

}
