<?php

namespace App\Services\CreateOrder\Strategies;

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
        return $apiResponse['id'] ?? null;
    }
}
