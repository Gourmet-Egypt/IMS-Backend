<?php

namespace App\Services\Steps\Pdf;

class TransformItemsStep
{
    public function handle($payload, \Closure $next)
    {
        $items = [];

        // Load transfer request with items (eager load the pivot data)
        $payload->purchaseOrder->load([
            'entries.transferRequest' => function($query) {
                $query->with('items');
            },
            'entries.item',
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

                $matchingItem = $entry->transferRequest->items->first(function($item) use ($entry) {
                    // item.ID (from transfer request via HQID) should equal entry.ItemID (Item.ID)
                    return $item->ID === $entry->ItemID;
                });

                if ($matchingItem && isset($matchingItem->pivot->quantity)) {
                    $quantityRequested = $matchingItem->pivot->quantity;
                }
            }

            // Sum quantity_issued for all infos of the same item
            $totalQuantityIssued = $entry->infos->sum('quantity_issued');
            $totalQuantityReceived = $entry->QuantityReceived;

            // For the stored PDF (default perspective), show actual values
            // The email PDFs will handle perspective-based values differently

            // Get the first info for production and expiration dates
            $firstInfo = $entry->infos->first();

            $items[] = [
                'lookupcode' => $entry->item->ItemLookupCode ?? '',
                'description' => $entry->ItemDescription,
                'quantity_requested' => $quantityRequested,
                'quantity_received' => $totalQuantityReceived,
                'production_date' => $firstInfo?->production_date,
                'expire_date' => $firstInfo?->expire_date,
                'quantity_issued' => $totalQuantityIssued,
                'sn' => $firstInfo?->SN,
            ];
        }

        $payload->items = collect($items)->map(fn($item) => (object) $item);

        return $next($payload);
    }
}
