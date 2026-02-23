<?php

namespace App\Services\Steps\CommitOrder;

use App\Enums\PurchaseOrderTypeEnum;
use App\Traits\Responses;
use Illuminate\Http\Response;

class ValidateStep
{
    use Responses;

    public function handle($payload, \Closure $next)
    {
        // Validate Cashier
        $cashier = $payload->request->user()->cashier;
        if (!$cashier) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Cashier not found'
            );
        }
        $payload->cashier = $cashier;

        // Validate Purchase Order Type
        $poTypeEnum = PurchaseOrderTypeEnum::tryFrom((int) $payload->purchaseOrder->POType);
        if (!$poTypeEnum) {
            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Invalid purchase order type'
            );
        }
        $payload->poTypeEnum = $poTypeEnum->name;

        return $next($payload);
    }
}
