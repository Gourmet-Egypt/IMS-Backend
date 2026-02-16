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
        \Illuminate\Support\Facades\Log::info("Validating Purchase Order #{$payload->purchaseOrder->ID}", [
            'purchase_order_id' => $payload->purchaseOrder->ID,
            'po_number' => $payload->purchaseOrder->PONumber,
            'po_type' => $payload->purchaseOrder->POType,
        ]);

        // Validate Cashier
        $cashier = $payload->request->user()->cashier;
        if (!$cashier) {
            \Illuminate\Support\Facades\Log::error("Cashier not found for Purchase Order #{$payload->purchaseOrder->ID}");
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Cashier not found'
            );
        }
        $payload->cashier = $cashier;

        // Validate Purchase Order Type
        $poTypeEnum = PurchaseOrderTypeEnum::tryFrom((int) $payload->purchaseOrder->POType);
        if (!$poTypeEnum) {
            \Illuminate\Support\Facades\Log::error("Invalid purchase order type for Purchase Order #{$payload->purchaseOrder->ID}", [
                'po_type' => $payload->purchaseOrder->POType,
            ]);
            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Invalid purchase order type'
            );
        }
        $payload->poTypeEnum = $poTypeEnum->name;

        \Illuminate\Support\Facades\Log::info("Validation successful for Purchase Order #{$payload->purchaseOrder->ID}", [
            'cashier_id' => $cashier->ID,
            'po_type_enum' => $poTypeEnum->name,
        ]);

        return $next($payload);
    }
}
