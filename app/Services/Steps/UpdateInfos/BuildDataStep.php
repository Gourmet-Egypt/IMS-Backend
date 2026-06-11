<?php

namespace App\Services\Steps\UpdateInfos;

class BuildDataStep
{
    public function handle($payload, \Closure $next)
    {
        $purchaseOrderEntry = $payload->purchaseOrderEntry;

        $payload->apiData = [
            "StoreID" => $payload->storeId,
            "transactionType" => $payload->poTypeEnum,
            "purchase_order_id" => $purchaseOrderEntry->PurchaseOrderID,
            "purchase_order_entry_id" => $purchaseOrderEntry->ID,
            "Batches" => $payload->validated['Batches'],
        ];

        return $next($payload);
    }
}
