<?php

namespace App\Services\Commit\Email;

use App\Models\PurchaseOrder;
use App\Services\Commit\Contracts\EmailPolicy;

/**
 * TransferOut: flow is FROM current store TO other store. Both the sending and
 * the receiving store are notified, each with the stock-out note of what was issued.
 */
class TransferOutEmailPolicy implements EmailPolicy
{
    public function recipientStoreIds(PurchaseOrder $order, object $config): array
    {
        return [$order->StoreID, $order->OtherStoreID];
    }

    public function perspective(PurchaseOrder $order, int $recipientStoreId): string
    {
        return 'from_store';
    }

    public function storeFlow(PurchaseOrder $order): array
    {
        // Transfer OUT: goods flow FROM current store TO other store.
        return [
            $order->currentStore->Name ?? 'Unknown',
            $order->otherStore->Name ?? 'Unknown',
        ];
    }

    public function senderStoreName(PurchaseOrder $order, string $perspective): string
    {
        return $order->currentStore->Name ?? 'Unknown Store';
    }
}
