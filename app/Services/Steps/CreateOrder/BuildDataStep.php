<?php

namespace App\Services\Steps\CreateOrder;

use App\Enums\TransferRequestTypeEnum;
use App\Services\CreateOrder\StrategyFactory;

class BuildDataStep
{
    public function __construct(private StrategyFactory $strategies) {}

    public function handle($payload, \Closure $next)
    {
        $transferRequest = $payload->transferRequest;
        $type = TransferRequestTypeEnum::from($transferRequest->type);
        $strategy = $this->strategies->for($type);

        $payload->apiData = [
            "Order" => [
                "POTitle" => $transferRequest->title,
                "transactionType" => $transferRequest->type,
                "StoreID" => (int) $transferRequest->store_id,
                "HH_ID" => (string) $transferRequest->id,
                "CashierID" => $payload->cashier->ID,
                ...$strategy->buildOrderFields($transferRequest),
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
