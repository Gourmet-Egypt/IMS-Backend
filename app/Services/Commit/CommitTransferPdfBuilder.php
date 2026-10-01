<?php

namespace App\Services\Commit;

use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

/**
 * Builds the perspective-specific PO PDF for the rebuilt commit flow (emails and
 * the stored/printed document both go through here). Renders the new commit
 * blades. Item transform shows Diff only on the last batch row of an item.
 */
class CommitTransferPdfBuilder
{
    public function build(PurchaseOrder $order, string $perspective)
    {
        // Fall back to the configured store when the PO has none.
        if (empty($order->StoreID) || $order->StoreID == 0) {
            $order->StoreID = DB::table('Configuration')->value('StoreID');
        }

        $order->load([
            'condition',
            'entries.infos',
            'entries.itemById',
            'entries.transferRequest' => fn ($q) => $q->with('items'),
            'currentStore',
            'otherStore',
        ]);

        $items = $this->transformItems($order, $perspective);

        $paperSize = strtoupper(config('app.pdf_paper_size', 'A4'));
        $styleView = $paperSize === 'A5' ? 'commit.pdfs.styles.a5' : 'commit.pdfs.styles.a4';

        // One blade per perspective — no perspective conditionals in the templates.
        $view = match ($perspective) {
            'from_store' => 'commit.pdfs.from_store',
            'to_store'   => 'commit.pdfs.to_store',
            default      => 'commit.pdfs.default',
        };

        $pdf = Pdf::loadView($view, [
            'purchaseOrder' => $order,
            'items' => $items,
            'condition' => $order->condition,
            'perspective' => $perspective,
            'styleView' => $styleView,
        ]);

        // Always A4 for printer compatibility (the A5 template is A4 with content
        // in the top half, cut after printing).
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * One row per batch. Diff is a reconciliation total, so it is shown only on
     * the last batch row of an item (null on the others), like quantity_requested
     * is shown only on the first row.
     *   from_store (OUT): Diff = issued - ordered
     *   to_store   (IN) : Diff = received(quantity_IN) - issued
     */
    private function transformItems(PurchaseOrder $order, string $perspective)
    {
        $items = [];

        foreach ($order->entries as $entry) {
            $quantityRequested = $entry->QuantityOrdered;

            if ($entry->transferRequest && $entry->transferRequest->items) {
                $matchingItem = $entry->transferRequest->items->first(
                    fn ($item) => $item->ID === $entry->ItemID
                );

                if ($matchingItem && isset($matchingItem->pivot->quantity)) {
                    $quantityRequested = $matchingItem->pivot->quantity;
                }
            }

            $cumulativeIssued = 0;
            $cumulativeIN = 0;
            $isFirstRow = true;
            $lastIndex = $entry->infos->count() - 1;

            foreach ($entry->infos as $index => $info) {
                $cumulativeIssued += $info->quantity_issued ?? 0;
                $cumulativeIN += $info->quantity_IN ?? 0;

                $diff = $index === $lastIndex
                    ? ($perspective === 'from_store'
                        ? $cumulativeIssued - $quantityRequested
                        : $cumulativeIN - $cumulativeIssued)
                    : null;

                $items[] = [
                    'lookupcode' => $entry->itemById->ItemLookupCode ?? '',
                    'description' => $entry->ItemDescription,
                    'quantity_requested' => $isFirstRow ? $quantityRequested : null,
                    'quantity_received' => $entry->QuantityReceived,
                    'quantity_IN' => $info->quantity_IN ?? 0,
                    'production_date' => $info->production_date,
                    'expire_date' => $info->expire_date,
                    'quantity_issued' => $info->quantity_issued ?? 0,
                    'diff' => $diff,
                    'sn' => $info->SN,
                ];

                $isFirstRow = false;
            }

            if ($entry->infos->isEmpty()) {
                $items[] = [
                    'lookupcode' => $entry->itemById->ItemLookupCode ?? '',
                    'description' => $entry->ItemDescription,
                    'quantity_requested' => $quantityRequested,
                    'quantity_received' => $entry->QuantityReceived,
                    'quantity_IN' => 0,
                    'production_date' => null,
                    'expire_date' => null,
                    'quantity_issued' => 0,
                    'diff' => $perspective === 'from_store' ? -$quantityRequested : 0,
                    'sn' => null,
                ];
            }
        }

        return collect($items)->map(fn ($item) => (object) $item);
    }
}
