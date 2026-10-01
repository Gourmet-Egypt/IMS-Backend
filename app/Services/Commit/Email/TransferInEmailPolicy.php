<?php

namespace App\Services\Commit\Email;

use App\Models\PurchaseOrder;
use App\Services\Commit\Contracts\EmailPolicy;

/**
 * TransferIN: flow is FROM current store TO other store; the label is
 * "Transfer IN" for the current store.
 */
class TransferInEmailPolicy implements EmailPolicy
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
            return 'to_store';
        }
        if ($other && $recipientStoreId === $other) {
            return 'from_store';
        }

        return 'to_store';
    }

    public function storeFlow(PurchaseOrder $order): array
    {
        // Transfer IN: goods flow FROM other store TO current store.
        return [
            $order->otherStore->Name ?? 'Unknown',
            $order->currentStore->Name ?? 'Unknown',
        ];
    }

    public function senderStoreName(PurchaseOrder $order, string $perspective): string
    {
        return $perspective === 'to_store'
            ? ($order->currentStore->Name ?? 'Unknown Store')
            : ($order->otherStore->Name ?? 'Unknown Store');
    }
}
