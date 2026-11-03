<?php

namespace App\Services\Commit\Email;

use App\Models\PurchaseOrder;
use App\Services\Commit\Contracts\EmailPolicy;

/**
 * Supplier PO: no other-store counterparty, so recipients are the configured
 * store; perspective is "supplier" for a PO and "return_to_supplier" for a return.
 */
class SupplierEmailPolicy implements EmailPolicy
{
    public function recipientStoreIds(PurchaseOrder $order, object $config): array
    {
        return [$config->StoreID];
    }

    public function perspective(PurchaseOrder $order, int $recipientStoreId): string
    {
        return (int) $order->OtherStoreID === 0 && (int) $order->SupplierID !== 0
            && in_array((int) $order->POType, [3, 5], true)
            ? 'return_to_supplier'
            : 'supplier';
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
