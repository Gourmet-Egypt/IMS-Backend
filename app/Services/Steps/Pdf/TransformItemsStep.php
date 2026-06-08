<?php

namespace App\Services\Steps\Pdf;

class TransformItemsStep
{
    public function handle($payload, \Closure $next)
    {
        $items = [];

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

            // Create one row per info (batch) for each entry
            // Track remaining quantity for diff calculation
            $remainingQuantity = $quantityRequested;
            $isFirstRow = true;

            foreach ($entry->infos as $info) {
                // Subtract current quantity from remaining to get diff
                $currentQuantity = $info->quantity_issued ?? $info->quantity_IN ?? 0;
                $remainingQuantity -= $currentQuantity;

                $items[] = [
                    'lookupcode' => $entry->itemById->ItemLookupCode ?? '',
                    'description' => $entry->ItemDescription,
                    'quantity_requested' => $isFirstRow ? $quantityRequested : null,
                    'quantity_received' => $entry->QuantityReceived,
                    'quantity_IN' => $info->quantity_IN ?? 0,
                    'production_date' => $info->production_date,
                    'expire_date' => $info->expire_date,
                    'quantity_issued' => $info->quantity_issued ?? 0,
                    'diff' => $remainingQuantity,
                    'sn' => $info->SN,
                ];

                $isFirstRow = false;
            }

            // If no infos exist, still add one row for the entry
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
                    'diff' => -$quantityRequested,
                    'sn' => null,
                ];
            }
        }

        $payload->items = collect($items)->map(fn($item) => (object) $item);

        return $next($payload);
    }
}
