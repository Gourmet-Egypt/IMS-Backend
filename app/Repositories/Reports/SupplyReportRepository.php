<?php

namespace App\Repositories\Reports;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderEntry;
use App\Models\TransferredItemInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SupplyReportRepository implements SupplyReportRepositoryInterface
{
    private const ORDER_RELATIONS = ['currentStore', 'supplier', 'storeEntries.itemById'];

    public function received(ReportFilters $filters): Collection
    {
        return $this->orders(PurchaseOrder::SUPPLIER_ORDER_TYPES, $filters)
            ->with(self::ORDER_RELATIONS)
            ->get();
    }

    public function returned(ReportFilters $filters): Collection
    {
        return $this->orders(PurchaseOrder::SUPPLIER_RETURN_TYPES, $filters)
            ->with(self::ORDER_RELATIONS)
            ->get();
    }

    public function productionDates(ReportFilters $filters): Collection
    {
        return TransferredItemInfo::on('sqlsrv_rms')
            ->whereHas('purchaseOrder', fn (Builder $q) => $q
                ->supplierOrder(PurchaseOrder::SUPPLIER_ORDER_TYPES)
                ->createdBetween($filters->from, $filters->to)
                ->when($filters->storeId, fn (Builder $q) => $q->where('StoreID', $filters->storeId)))
            ->with(['purchaseOrder.currentStore', 'purchaseOrder.supplier', 'entry.itemById', 'entry.stock'])
            ->orderByDesc('purchase_order_id')
            ->orderBy('ID')
            ->get();
    }

    public function missingOnOrder(ReportFilters $filters): Collection
    {
        $missing = fn ($q) => $q->whereColumn('QuantityOrdered', '>', 'QuantityReceivedToDate');

        return $this->orders(PurchaseOrder::SUPPLIER_ORDER_TYPES, $filters)
            ->where('Status', '>', 0)
            ->whereHas('storeEntries', $missing)
            ->with(['currentStore', 'supplier', 'storeEntries' => $missing, 'storeEntries.itemById'])
            ->get();
    }

    public function checklist(ReportFilters $filters): Collection
    {
        return $this->orders(PurchaseOrder::SUPPLIER_ORDER_TYPES, $filters)
            ->with(['currentStore', 'supplier', 'storeCondition.goodsType', 'storeEntries.itemById.department'])
            ->get();
    }

    public function netPurchase(ReportFilters $filters): Collection
    {
        return $this->orders(array_merge(PurchaseOrder::SUPPLIER_ORDER_TYPES, PurchaseOrder::SUPPLIER_RETURN_TYPES), $filters)
            ->with(self::ORDER_RELATIONS)
            ->get()
            ->flatMap(function (PurchaseOrder $order) {
                $sign = in_array((int) $order->POType, PurchaseOrder::SUPPLIER_RETURN_TYPES, true) ? -1 : 1;

                return $order->storeEntries->map(fn (PurchaseOrderEntry $entry) => [
                    'item_lookup_code' => $entry->itemById?->ItemLookupCode,
                    'description' => $entry->ItemDescription,
                    'supplier' => $order->supplier?->SupplierName,
                    'store' => $order->currentStore?->Name,
                    'month' => $order->createdAt()?->format('Y-m'),
                    'qty' => $sign * (float) $entry->QuantityReceivedToDate,
                    'cost' => $sign * (float) $entry->QuantityReceivedToDate * (float) $entry->Price,
                ]);
            })
            ->groupBy(fn (array $line) => implode('|', [$line['month'], $line['item_lookup_code'] ?? $line['description'], $line['supplier'], $line['store']]))
            ->map(function (Collection $lines) {
                $first = $lines->first();

                return [
                    'item_lookup_code' => $first['item_lookup_code'],
                    'description' => $first['description'],
                    'supplier' => $first['supplier'],
                    'store' => $first['store'],
                    'month' => $first['month'],
                    'net_qty' => $lines->sum('qty'),
                    'net_total_cost' => round($lines->sum('cost'), 4),
                ];
            })
            ->sortBy([['month', 'desc'], ['item_lookup_code', 'asc']])
            ->values();
    }

    public function counts(ReportFilters $filters): array
    {
        $orders = fn () => $this->orders(PurchaseOrder::SUPPLIER_ORDER_TYPES, $filters)->reorder();

        return [
            'count_of_pos' => $orders()->count(),
            'count_of_bonus' => null,
            'count_of_returns' => $this->orders(PurchaseOrder::SUPPLIER_RETURN_TYPES, $filters)->reorder()->count(),
            'count_of_pos_created_from_hh' => $orders()->where('PONumber', 'like', 'HH[_]%')->count(),
            'count_of_pos_created_by_ge' => $orders()->where('POType', 1)->count(),
            'count_of_orders_received_with_partial_ord' => $orders()->whereHas('storeEntries', fn ($q) => $q
                ->whereColumn('QuantityReceivedToDate', '<', 'QuantityOrdered'))->count(),
        ];
    }

    private function orders(array $types, ReportFilters $filters): Builder
    {
        return PurchaseOrder::onSecondary()
            ->supplierOrder($types)
            ->createdBetween($filters->from, $filters->to)
            ->when($filters->storeId, fn (Builder $q) => $q->where('StoreID', $filters->storeId))
            ->latest('DateCreated')
            ->orderBy('ID');
    }
}
