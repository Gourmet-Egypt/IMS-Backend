<?php

namespace App\Services\Steps\CreateOrder;

class BuildDataStep
{
    public function handle($payload, \Closure $next)
    {
        $transferRequest = $payload->transferRequest;

        $payload->apiData = [
            "Order" => [
                "POTitle" => $transferRequest->title,
                "transactionType" => $transferRequest->type,
                "StoreID" => (int) $transferRequest->store_id,
                "OtherStoreID" => (int) $transferRequest->other_store_id,
                "SupplierID" => 0,
                "HH_ID" => (string) $transferRequest->id,
                "CashierID" => $payload->cashier->ID,
            ],
            "OrderItems" => $transferRequest->items->map(function ($item) {
                return [
                    "ItemLookupcode" => (string) $item->ItemLookupCode,
                    "QTY" => (float) $item->pivot->quantity,
                ];
            })->values()->toArray(),
        ];

        return $next($payload);
    }
}
