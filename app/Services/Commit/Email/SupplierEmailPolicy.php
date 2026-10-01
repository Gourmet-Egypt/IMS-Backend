<?php

namespace App\Services\Commit\Email;

use App\Models\PurchaseOrder;
use App\Services\Commit\Contracts\EmailPolicy;

/**
 * Supplier PO: no other-store counterparty, so recipients are the configured
 * store and the notification uses the neutral "default" perspective.
 */
class SupplierEmailPolicy implements EmailPolicy
{
    public function recipientStoreIds(PurchaseOrder $order, object $config): array
    {
        return [$config->StoreID];
    }

    public function perspective(PurchaseOrder $order, int $recipientStoreId): string
    {
        return 'default';
    }

    public function storeFlow(PurchaseOrder $order): array
    {
        // Supplier PO has no other-store counterparty.
        return [$order->currentStore->Name ?? 'Unknown', ''];
    }

    public function senderStoreName(PurchaseOrder $order, string $perspective): string
    {
        return $order->currentStore->Name ?? 'Unknown Store';
    }
}
