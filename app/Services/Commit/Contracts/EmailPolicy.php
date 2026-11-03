<?php

namespace App\Services\Commit\Contracts;

use App\Models\PurchaseOrder;

/**
 * Per-type email behavior: who gets notified and from which perspective.
 * One implementation per transaction type, so there is no shared "default"
 * conditional smeared across the send step.
 */
interface EmailPolicy
{
    /**
     * Store ids whose recipients should be notified for this order.
     */
    public function recipientStoreIds(PurchaseOrder $order, object $config): array;

    /**
     * The notification perspective for a recipient in the given store.
     */
    public function perspective(PurchaseOrder $order, int $recipientStoreId): string;

    /**
     * The goods-flow store names for this type as [fromName, toName].
     */
    public function storeFlow(PurchaseOrder $order): array;

    /**
     * The sender (envelope "from") store name for the given perspective.
     */
    public function senderStoreName(PurchaseOrder $order, string $perspective): string;
}
