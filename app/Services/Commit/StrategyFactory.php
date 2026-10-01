<?php

namespace App\Services\Commit;

use App\Enums\PurchaseOrderTypeEnum;
use App\Services\Commit\Contracts\TransactionTypeStrategy;
use App\Services\Commit\Strategies\SupplierStrategy;
use App\Services\Commit\Strategies\TransferInStrategy;
use App\Services\Commit\Strategies\TransferOutStrategy;
use Illuminate\Http\Response;

/**
 * The ONE place that maps a purchase order type to a strategy. HQ types map to
 * the same strategies as their standard equivalents (4 = TransferIN, 5 = TransferOut).
 *
 * NOTE: the three concrete strategy classes below are created in the next step;
 * resolving one before it exists will fail, which is expected while we build.
 */
class StrategyFactory
{
    public function for(PurchaseOrderTypeEnum $type): TransactionTypeStrategy
    {
        $class = match ($type) {
            PurchaseOrderTypeEnum::TransferIN,
            PurchaseOrderTypeEnum::TRANSFER_IN_HQ => TransferInStrategy::class,

            PurchaseOrderTypeEnum::TransferOut,
            PurchaseOrderTypeEnum::TRANSFER_OUT_HQ => TransferOutStrategy::class,

            PurchaseOrderTypeEnum::LOCAL_PO_SUPPLIER_0,
            PurchaseOrderTypeEnum::LOCAL_PO_SUPPLIER_1 => SupplierStrategy::class,

            default => throw new CommitException(
                'Unsupported purchase order type',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ),
        };

        return app($class);
    }
}
