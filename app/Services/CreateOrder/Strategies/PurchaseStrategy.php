<?php

namespace App\Services\CreateOrder\Strategies;

use App\Models\TransferRequest;
use App\Services\CreateOrder\Contracts\OrderTypeStrategy;

class PurchaseStrategy implements OrderTypeStrategy
{
    public function buildOrderFields(TransferRequest $transferRequest): array
    {
        return [
            'OtherStoreID' => 0,
            'SupplierID' => (int) $transferRequest->supplier_id,
        ];
    }

    public function validate(TransferRequest $transferRequest): ?string
    {
        return $transferRequest->supplier_id ? null : 'A supplier is required for this order type.';
    }

    public function resolvePurchaseOrderId(TransferRequest $transferRequest, array $apiResponse): ?int
    {
        return $apiResponse['id'] ?? null;
    }
}
