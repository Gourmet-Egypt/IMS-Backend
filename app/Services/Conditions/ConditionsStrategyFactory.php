<?php

namespace App\Services\Conditions;

use App\Enums\PurchaseOrderTypeEnum;
use App\Services\Commit\CommitException;
use App\Services\Conditions\Strategies\PurchaseOrderConditionsStrategy;
use Illuminate\Http\Response;

/**
 * Conditions are only saved for POs: 0 (PO) and 1 (PO_HQ).
 */
class ConditionsStrategyFactory
{
    public function for(PurchaseOrderTypeEnum $type): ConditionsStrategy
    {
        $class = match ($type) {
            PurchaseOrderTypeEnum::PO,
            PurchaseOrderTypeEnum::PO_HQ => PurchaseOrderConditionsStrategy::class,

            default => throw new CommitException(
                'Conditions are not supported for this purchase order type',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ),
        };

        return app($class);
    }
}
