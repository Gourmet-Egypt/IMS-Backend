<?php

namespace App\Services\CreateOrder\Contracts;

use App\Models\TransferRequest;

interface OrderTypeStrategy
{
    public function buildOrderFields(TransferRequest $transferRequest): array;

    // Returns an error message if the type's required field is missing, null if valid.
    public function validate(TransferRequest $transferRequest): ?string;

    // Returns null when the id will be set asynchronously.
    public function resolvePurchaseOrderId(TransferRequest $transferRequest, array $apiResponse): ?int;
}
