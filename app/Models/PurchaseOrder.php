<?php

namespace App\Models;

use App\Models\Concerns\HasStoreRelations;
use App\Models\Relations\HasManyInStore;
use App\Models\Relations\HasOneInStore;
use App\Traits\Responses;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class PurchaseOrder extends Model
{
    use HasFactory, Responses, HasStoreRelations;

    protected $table = 'PurchaseOrder';
    protected $primaryKey = 'ID';

    protected $hidden = ['DBTimeStamp'];

    public const TRANSFER_IN_TYPES = [2, 4];
    public const TRANSFER_OUT_TYPES = [3, 5];
    public const SUPPLIER_ORDER_TYPES = [0, 1];
    public const SUPPLIER_RETURN_TYPES = [3, 5];

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
            'entries' => function ($query) use ($store_id) {
                $query->where('StoreID', $store_id);
            },
            'entries.infos',
            'entries.itemById:Cost,ItemLookupCode',
            'condition'
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

    // The receiving store's copy (PONumber "..._IMS<originId>") has no condition of its own.
    public function originCondition()
    {
        return $this->hasOne(TransferCondition::class, 'purchase_order_id', 'origin_po_id');
    }

    public function getOriginPoIdAttribute(): ?int
    {
        return preg_match('/_IMS(\d+)$/', (string) $this->PONumber, $m) ? (int) $m[1] : null;
    }

    public function getTransferConditionAttribute(): ?TransferCondition
    {
        return $this->condition ?? $this->originCondition;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'ID'); //-> return $this->belongsTo(Supplier::class, 'Supplierid', 'HQID');
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

    public function scopeStoreTransfer(Builder $query, array $types, string $alias = 'PurchaseOrder'): Builder
    {
        return $query->whereIn("$alias.POType", $types)
            ->where("$alias.SupplierID", 0)
            ->where("$alias.OtherStoreID", '<>', 0);
    }

    public function scopeSupplierOrder(Builder $query, array $types, string $alias = 'PurchaseOrder'): Builder
    {
        return $query->whereIn("$alias.POType", $types)
            ->where("$alias.SupplierID", '<>', 0)
            ->where("$alias.OtherStoreID", 0);
    }

    public function scopeCreatedBetween(Builder $query, $from, $to, string $alias = 'PurchaseOrder'): Builder
    {
        return $query->whereBetween("$alias.DateCreated", [$from, $to]);
    }

    public function storeEntries(): HasManyInStore
    {
        return $this->hasManyInStore(PurchaseOrderEntry::class, 'PurchaseOrderID', 'ID');
    }

    public function storeCondition(): HasOneInStore
    {
        return $this->hasOneInStore(TransferCondition::class, 'purchase_order_id', 'ID');
    }

    public function inventoryLog(): HasOneInStore
    {
        return $this->hasOneInStore(InventoryTransferLog::class, 'ReferenceID', 'ID')
            ->where('Type', InventoryTransferLog::TYPE_PURCHASE_ORDER)
            ->latest('DateTransferred')
            ->orderByDesc('ID');
    }

    public function receivingOrder(): HasOneInStore
    {
        return $this->hasOneInStore(
            self::class, 'PONumber', 'receiving_po_number', 'StoreID', 'OtherStoreID',
            fn (string $related, string $parent) => "$related.PONumber = RIGHT('00000' + CAST($parent.StoreID AS varchar(10)), 5) + '_' + $parent.PONumber",
        )->whereIn('POType', self::TRANSFER_IN_TYPES);
    }

    public function issuingOrder(): HasOneInStore
    {
        return $this->hasOneInStore(
            self::class, 'PONumber', 'issuing_po_number', 'StoreID', 'OtherStoreID',
            fn (string $related, string $parent) => "$parent.PONumber = RIGHT('00000' + CAST($related.StoreID AS varchar(10)), 5) + '_' + $related.PONumber",
        )->whereIn('POType', self::TRANSFER_OUT_TYPES);
    }

    public function getReceivingPoNumberAttribute(): string
    {
        return sprintf('%05d_%s', $this->StoreID, $this->PONumber);
    }

    public function getIssuingPoNumberAttribute(): ?string
    {
        return preg_match('/^\d{5}_(.+)$/', (string) $this->PONumber, $m) ? $m[1] : null;
    }

    public function createdAt(): ?Carbon
    {
        $value = $this->getRawOriginal('DateCreated');

        return $value ? Carbon::parse($value) : null;
    }

    public function receivedAt(): ?Carbon
    {
        $value = $this->getRawOriginal('LastUpdated');

        return $this->Status > 0 && $value ? Carbon::parse($value) : null;
    }

    public function transferLines(): Collection
    {
        $received = $this->receivingOrder?->storeEntries->keyBy('ItemID') ?? collect();

        return $this->storeEntries->map(function (PurchaseOrderEntry $entry) use ($received) {
            $in = $received->get($entry->ItemID);
            $qtyIssue = (float) $entry->QuantityReceivedToDate;
            $qtyReceived = (float) ($in?->QuantityReceivedToDate ?? 0);
            $issueCost = (float) $entry->Price;
            $receivedCost = (float) ($in?->Price ?? $entry->Price);

            return [
                'item_lookup_code' => $entry->itemById?->ItemLookupCode,
                'description' => $entry->ItemDescription,
                'qty_issue' => $qtyIssue,
                'qty_rcv' => $qtyReceived,
                'issue_cost' => $issueCost,
                'received_cost' => $receivedCost,
                'total_cost_issue' => round($issueCost * $qtyIssue, 4),
                'total_cost_rcv' => round($receivedCost * $qtyReceived, 4),
                'diff_cost' => round($receivedCost * $qtyReceived - $issueCost * $qtyIssue, 4),
            ];
        });
    }

    public function processStart()
    {
        return $this->hasOne(PurchaseOrderProcessStart::class, 'purchase_order_id', 'ID');
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
