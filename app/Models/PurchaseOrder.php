<?php

namespace App\Models;

use App\Traits\Responses;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class PurchaseOrder extends Model
{
    use HasFactory, Responses;

    protected $table = 'PurchaseOrder';

    protected $hidden = ['DBTimeStamp'];

    protected $casts = [
        'DateCreated' => 'date:Y-m-d',
        'LastUpdated' => 'date:Y-m-d',
    ];


    public static function store()
    {
        $storeId = request()->store_id;
        $status = request()->status;
        $types = request()->type;
        $month = request()->month ?? now()->month;
        $year = request()->year ?? now()->year;

        if ($month > now()->month) {
            $year = now()->year - 1;
        }

        $query = self::onSecondary()
            ->where('StoreID', $storeId)
            ->whereMonth('DateCreated', $month)
            ->whereYear('DateCreated', $year);


        if (!is_null($types) && !is_array($types)) {
            $types = [(int) $types];
        }


        if (!is_null($types) && !is_null($status)) {
            return [
                'type' => $types,
                'status' => (int) $status,
                'count' => $query->whereIn('POType', $types)
                    ->where('status', $status)
                    ->count(),
            ];
        }


        if (!is_null($types)) {
            $statusCounts = $query
                ->whereIn('POType', $types)
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');

            return [
                'type' => $types,
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

                'supplier' =>
                    ($typeCounts->get(0, 0)) +
                    ($typeCounts->get(1, 0)),
                'in' =>
                    ($typeCounts->get(2, 0)) +
                    ($typeCounts->get(4, 0)),
                'out' =>
                    ($typeCounts->get(3, 0)) +
                    ($typeCounts->get(5, 0)),
                'total' => $typeCounts->sum(),
            ];
        }
    }


    public static function onSecondary()
    {
        return static::on('sqlsrv_rms');
    }

    public static function allStores()
    {
        $month = request()->month ?? now()->month;
        $year = request()->year ?? now()->year;

        $purchaseOrders = self::onSecondary()
            ->whereMonth('DateCreated', $month)
            ->whereYear('DateCreated', $year)
            ->select('StoreID', 'POType', DB::raw('COUNT(*) as count'))
            ->groupBy('StoreID', 'POType')
            ->get();


        $stores = Store::select('ID', 'StoreCode', 'Name')->get();

        $result = $stores->map(function ($store) use ($purchaseOrders) {
            $storePOs = $purchaseOrders->where('StoreID', $store->ID);

            return [
                'store_id' => $store->ID,
                'store_name' => $store->Name,
                'in' => $storePOs->whereIn('POType', [2, 4])->sum('count'),
                'out' => $storePOs->whereIn('POType', [3, 5])->sum('count'),
            ];
        });

        return $result;
    }

    public function scopeStoreFilter(Builder $query): Builder
    {
        $store_id = request()->user()->store_id;
        $type = request('type');
        $status = request('status', 0);


        $new_query = $query->where([
            ['StoreID', $store_id],
            ['SupplierID', '=', 0],
            ['OtherStoreID', '<>', 0],
            ['Status', $status],
        ]);

        return $new_query;

        if (!$type) {
            return $new_query;
        } else {
            if (is_string($type)) {
                $type = json_decode($type, true) ?? explode(',', $type);
            }
            return $new_query->whereIn('POType', (array) $type);
        }
    }

    public function scopeTransferStatus($query, $id)
    {
        $store_id = request()->get('store_id');

        return $query->with([
            'entries.infos',
            'entries.item:Cost,ItemLookupCode',
        ])
            ->where([
                ['PONumber', $id],
                ['StoreID', $store_id]
            ]);
    }

    public function scopeTransferList($query)
    {
        $store_id = request()->get('store_id');

        return $query->where('StoreID', $store_id)
            ->whereMonth('DateCreated', now()->month)
            ->whereYear('DateCreated', now()->year);
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

}
