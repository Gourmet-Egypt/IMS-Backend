<?php

namespace App\Services\Commit\Email;

use App\Models\PurchaseOrder;
use App\Services\Commit\Contracts\EmailPolicy;

/**
 * TransferOut: flow is FROM current store TO other store; the label is
 * "Transfer OUT" for the current store.
 */
class TransferOutEmailPolicy implements EmailPolicy
{
    public function recipientStoreIds(PurchaseOrder $order, object $config): array
    {
        return [$order->StoreID];
    }

    public function perspective(PurchaseOrder $order, int $recipientStoreId): string
    {
        $current = (int) ($order->currentStore->ID ?? 0);
        $other   = (int) ($order->otherStore->ID ?? 0);

        if ($current && $recipientStoreId === $current) {
            return 'from_store';
        }
        if ($other && $recipientStoreId === $other) {
            return 'to_store';
        }

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
        return $perspective === 'from_store'
            ? ($order->currentStore->Name ?? 'Unknown Store')
            : ($order->otherStore->Name ?? 'Unknown Store');
    }
}
