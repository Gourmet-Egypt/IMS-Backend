<?php

namespace App\Services\Steps\UpdateInfos;

use App\Enums\PurchaseOrderTypeEnum;
use App\Traits\Responses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ValidateStep
{
    use Responses;

    public function handle($payload, \Closure $next)
    {
        $purchaseOrderEntry = $payload->purchaseOrderEntry;

        $poTypeEnum = PurchaseOrderTypeEnum::tryFrom($purchaseOrderEntry->purchaseOrder->POType);

        if (!$poTypeEnum) {
            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Invalid purchase order type'
            );
        }

        $payload->poTypeEnum = $poTypeEnum->apiTransactionType()->name;
        $payload->storeId = DB::table('Configuration')->select('StoreID')->value('StoreID');

        return $next($payload);
    }
}
