<?php

namespace App\Repositories\Reports;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProcessStart;
use App\Models\TransferredItemInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransferReportRepository implements TransferReportRepositoryInterface
{
    public function netTransfer(ReportFilters $filters): Collection
    {
        return $this->netTransferQuery($filters)->with('inventoryLog.cashier')->get();
    }

    public function netTransferSummary(ReportFilters $filters): Collection
    {
        return $this->netTransferQuery($filters)->get()
            ->flatMap(fn (PurchaseOrder $order) => $order->transferLines()->map(fn (array $line) => $line + [
                'store_issue' => $order->currentStore?->Name,
                'store_received' => $order->otherStore?->Name,
                'month' => $order->createdAt()?->format('Y-m'),
            ]))
            ->groupBy(fn (array $line) => implode('|', [$line['month'], $line['item_lookup_code'] ?? $line['description'], $line['store_issue'], $line['store_received']]))
            ->map(function (Collection $lines) {
                $first = $lines->first();

                return [
                    'item_lookup_code' => $first['item_lookup_code'],
                    'description' => $first['description'],
                    'store_issue' => $first['store_issue'],
                    'store_received' => $first['store_received'],
                    'month' => $first['month'],
                    'net_qty' => $lines->sum('qty_rcv') - $lines->sum('qty_issue'),
                    'net_total_cost' => round($lines->sum('total_cost_rcv') - $lines->sum('total_cost_issue'), 4),
                    'qty_issue' => $lines->sum('qty_issue'),
                    'qty_received' => $lines->sum('qty_rcv'),
                ];
            })
            ->sortBy([['month', 'desc'], ['item_lookup_code', 'asc']])
            ->values();
    }

    public function notReceived(ReportFilters $filters): Collection
    {
        return $this->transfers(PurchaseOrder::TRANSFER_OUT_TYPES, $filters)
            ->when($filters->storeId, fn (Builder $q) => $q->where('OtherStoreID', $filters->storeId))
            ->whereDoesntHave('receivingOrder', fn (Builder $q) => $q->where('Status', '>', 0))
            ->with(['currentStore', 'otherStore', 'receivingOrder'])
            ->get();
    }

    public function checklist(ReportFilters $filters, string $direction): Collection
    {
        $isOut = $direction === 'out';

        return $this->transfers($isOut ? PurchaseOrder::TRANSFER_OUT_TYPES : PurchaseOrder::TRANSFER_IN_TYPES, $filters)
            ->when($filters->storeId, fn (Builder $q) => $q->where('StoreID', $filters->storeId))
            ->with(array_merge(
                ['currentStore', 'storeCondition.goodsType', 'storeEntries.itemById.department'],
                $isOut ? [] : ['issuingOrder.storeCondition.goodsType'],
            ))
            ->get();
    }

    public function productionDates(ReportFilters $filters, string $direction): Collection
    {
        $types = $direction === 'out' ? PurchaseOrder::TRANSFER_OUT_TYPES : PurchaseOrder::TRANSFER_IN_TYPES;

        return TransferredItemInfo::on('sqlsrv_rms')
            ->whereHas('purchaseOrder', fn (Builder $q) => $q
                ->storeTransfer($types)
                ->createdBetween($filters->from, $filters->to)
                ->when($filters->storeId, fn (Builder $q) => $q->where('StoreID', $filters->storeId)))
            ->with([
                'purchaseOrder.currentStore',
                'purchaseOrder.otherStore',
                'entry.itemById',
                'entry.stock',
            ])
            ->orderByDesc('purchase_order_id')
            ->orderBy('ID')
            ->get();
    }

    public function processTime(ReportFilters $filters, string $direction): Collection
    {
        $starts = PurchaseOrderProcessStart::on('sqlsrv_rms')
            ->whereNotNull('end_date')
            ->whereBetween('start_date', [$filters->from, $filters->to])
            ->get()
            ->keyBy('purchase_order_id');

        $orders = $starts->isEmpty() ? collect() : PurchaseOrder::onSecondary()
            ->with('currentStore')
            ->when($filters->storeId, fn (Builder $q) => $q->where('StoreID', $filters->storeId))
            ->whereIn('ID', $starts->keys())
            ->storeTransfer($direction === 'out' ? PurchaseOrder::TRANSFER_OUT_TYPES : PurchaseOrder::TRANSFER_IN_TYPES)
            ->get();

        $minutes = $orders->mapWithKeys(fn (PurchaseOrder $order) => [
            $order->ID => round($starts[$order->ID]->start_date->diffInSeconds($starts[$order->ID]->end_date, true) / 60, 2),
        ]);
        $average = $minutes->isEmpty() ? null : round($minutes->avg(), 2);

        return $orders->map(fn (PurchaseOrder $order) => [
            $direction === 'out' ? 'store_issue' : 'store_received' => $order->currentStore?->Name,
            'order_number' => $order->PONumber,
            'start_time' => $starts[$order->ID]->start_date->toDateTimeString(),
            'end_time' => $starts[$order->ID]->end_date->toDateTimeString(),
            'duration_minutes' => $minutes[$order->ID],
            $direction === 'out' ? 'average_issue_time_minutes' : 'average_receiving_time_minutes' => $average,
        ])->sortByDesc('start_time')->values();
    }

    private function transfers(array $types, ReportFilters $filters): Builder
    {
        return PurchaseOrder::onSecondary()
            ->storeTransfer($types)
            ->createdBetween($filters->from, $filters->to)
            ->latest('DateCreated')
            ->orderBy('ID');
    }

    private function netTransferQuery(ReportFilters $filters): Builder
    {
        return $this->transfers(PurchaseOrder::TRANSFER_OUT_TYPES, $filters)
            ->when($filters->storeId, fn (Builder $q) => $q->where(fn (Builder $w) => $w
                ->where('StoreID', $filters->storeId)
                ->orWhere('OtherStoreID', $filters->storeId)))
            ->with([
                'currentStore',
                'otherStore',
                'storeEntries.itemById',
                'receivingOrder.storeEntries',
            ]);
    }
}
