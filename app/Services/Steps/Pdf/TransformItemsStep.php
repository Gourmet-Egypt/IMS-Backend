<?php

namespace App\Services\Steps\Pdf;

class TransformItemsStep
{
    public function handle($payload, \Closure $next)
    {
        $items = [];

        // Perspective drives how the Diff column is computed.
        // Mirrors the mapping used in GeneratePdfStep.
        //   from_store (Transfer OUT): Diff = issued - ordered
        //   to_store   (Transfer IN) : Diff = received(quantity_IN) - issued
        $perspective = match ((string) $payload->purchaseOrder->POType) {
            '2', '4' => 'to_store',
            '3', '5' => 'from_store',
            default  => 'from_store',
        };

        // Load transfer request with items (eager load the pivot data)
        $payload->purchaseOrder->load([
            'entries.transferRequest' => function ($query) {
                $query->with('items');
            },
            'entries.itemById',
            'entries.infos'
        ]);

        foreach ($payload->purchaseOrder->entries as $entry) {
            // Get quantity from transfer request item
            $quantityRequested = $entry->QuantityOrdered; // Default

            if ($entry->transferRequest && $entry->transferRequest->items) {
                // Match by Item.ID since:
                // - transfer_request_item.item_id -> Item.HQID
                // - PurchaseOrderEntry.ItemID -> Item.ID
                // Both refer to the same item but use different keys

                $matchingItem = $entry->transferRequest->items->first(function ($item) use ($entry) {
                    // item.ID (from transfer request via HQID) should equal entry.ItemID (Item.ID)
                    return $item->ID === $entry->ItemID;
                });

                if ($matchingItem && isset($matchingItem->pivot->quantity)) {
                    $quantityRequested = $matchingItem->pivot->quantity;
                }
            }

            // Create one row per info (batch) for each entry.
            // The batches all belong to the same item, so the Diff is only a
            // meaningful reconciliation once issued is fully totalled. It is
            // therefore shown only on the last batch row (null on the others),
            // mirroring how quantity_requested is shown only on the first row.
            $cumulativeIssued = 0;
            $cumulativeIN = 0;
            $isFirstRow = true;
            $lastIndex = $entry->infos->count() - 1;

            foreach ($entry->infos as $index => $info) {
                $cumulativeIssued += $info->quantity_issued ?? 0;
                $cumulativeIN += $info->quantity_IN ?? 0;

                // from_store (OUT): issued - ordered
                // to_store   (IN) : received(quantity_IN) - issued
                // Computed from the cumulative totals, attached only to the last row.
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

            // If no infos exist, still add one row for the entry.
            // OUT: 0 issued - ordered = -ordered; IN: 0 received - 0 issued = 0.
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

        $payload->items = collect($items)->map(fn($item) => (object) $item);

        return $next($payload);
    }
}
