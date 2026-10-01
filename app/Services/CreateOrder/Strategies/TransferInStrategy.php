<?php

namespace App\Services\CreateOrder\Strategies;

use App\Jobs\SyncPurchaseOrderJob;
use App\Models\TransferRequest;
use App\Services\CreateOrder\Contracts\OrderTypeStrategy;

class TransferInStrategy implements OrderTypeStrategy
{
    public function buildOrderFields(TransferRequest $transferRequest): array
    {
        return [
            'OtherStoreID' => (int) $transferRequest->other_store_id,
            'SupplierID' => 0,
        ];
    }

    public function validate(TransferRequest $transferRequest): ?string
    {
        return $transferRequest->other_store_id ? null : 'A destination store is required for this transfer type.';
    }

    public function resolvePurchaseOrderId(TransferRequest $transferRequest, array $apiResponse): ?int
    {
        $purchaseOrderNumber = sprintf(
            '%05d_%05d_%s',
            $transferRequest->other_store_id,
            $transferRequest->store_id,
            $apiResponse['poNumber']
        );

        SyncPurchaseOrderJob::dispatch($transferRequest->id, $purchaseOrderNumber)
            ->delay(now()->addMinutes(3));

        return null;
    }
}
