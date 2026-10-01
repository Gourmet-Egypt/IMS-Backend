<?php

namespace App\Services\CreateOrder\Strategies;

use App\Models\TransferRequest;

class TransferOutStrategy extends TransferInStrategy
{
    public function resolvePurchaseOrderId(TransferRequest $transferRequest, array $apiResponse): ?int
    {
        return $apiResponse['id'] ?? null;
    }
}
