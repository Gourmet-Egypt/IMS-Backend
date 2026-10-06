<?php

namespace App\Services\Commit;

use App\Enums\PurchaseOrderTypeEnum;
use App\Models\PurchaseOrder;
use App\Services\Commit\Contracts\TransactionTypeStrategy;
use App\Services\Commit\Strategies\ReturnToSupplierStrategy;
use App\Services\Commit\Strategies\SupplierStrategy;
use App\Services\Commit\Strategies\TransferInStrategy;
use App\Services\Commit\Strategies\TransferOutStrategy;
use Illuminate\Http\Response;

/**
 * The ONE place that maps a purchase order to a strategy. The operation comes
 * from the type plus the order's IDs: 0/1 = PO, 2/4 = TransferIN, and 3/5 are
 * ReturnToSupplier (SupplierID set, no OtherStoreID) or TransferOut (the opposite).
 */
class StrategyFactory
{
    public function for(PurchaseOrderTypeEnum $type, PurchaseOrder $order): TransactionTypeStrategy
    {
        $class = match ($type->operation((int) $order->SupplierID, (int) $order->OtherStoreID)) {
            'PO' => SupplierStrategy::class,
            'TransferIN' => TransferInStrategy::class,
            'TransferOut' => TransferOutStrategy::class,
            'ReturnToSupplier' => ReturnToSupplierStrategy::class,
            null => throw new CommitException(
                'Order must have either OtherStoreID or SupplierID',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ),
        };

        return app($class);
    }
}
