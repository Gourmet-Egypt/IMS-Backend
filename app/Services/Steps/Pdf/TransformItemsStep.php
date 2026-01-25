<?php

namespace App\Services\Steps\Pdf;

class TransformItemsStep
{
    public function handle($payload, \Closure $next)
    {
        $items = [];

        foreach ($payload->purchaseOrder->entries as $entry) {
            if ($entry->infos->isEmpty()) {
                $items[] = [
                    'lookupcode' => $entry->item->ItemLookupCode ?? '',
                    'description' => $entry->ItemDescription,
                    'quantity_requested' => $entry->QuantityOrdered,
                    'quantity_received' => $entry->QuantityReceived,
                    'production_date' => null,
                    'expire_date' => null,
                    'quantity_issued' => 0,
                    'sn' => null,
                ];
            } else {
                foreach ($entry->infos as $info) {
                    $items[] = [
                        'lookupcode' => $entry->item->ItemLookupCode ?? '',
                        'description' => $entry->ItemDescription,
                        'quantity_requested' => $entry->QuantityOrdered,
                        'quantity_received' => $entry->QuantityReceived,
                        'production_date' => $info->production_date,
                        'expire_date' => $info->expire_date,
                        'quantity_issued' => $info->quantity_issued,
                        'sn' => $info->SN,
                    ];
                }
            }
        }

        $payload->items = collect($items)->map(fn($item) => (object) $item);

        return $next($payload);
    }
}
