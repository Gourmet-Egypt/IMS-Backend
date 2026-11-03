<?php

namespace App\Services\Commit;

use App\Enums\PurchaseOrderTypeEnum;
use App\Models\Cashier;
use App\Models\PurchaseOrder;
use App\Services\Commit\Contracts\TransactionTypeStrategy;
use Illuminate\Http\Request;

/**
 * Everything a single commit needs, threaded through the flow. Built once in
 * CommitService::prepare() and read-only thereafter.
 */
class CommitContext
{
    public function __construct(
        public PurchaseOrder $order,
        public Request $request,
        public Cashier $cashier,
        public int $storeId,
        public PurchaseOrderTypeEnum $apiType,
        public TransactionTypeStrategy $strategy,
    ) {}

    /**
     * The order fields every transaction type's payload starts from.
     * Shared by composition so no strategy has to repeat them.
     */
    public function basePayload(): array
    {
        return [
            'ID'              => (int) $this->order->ID,
            'transactionType' => $this->apiType->name,
            'StoreID'         => (int) $this->storeId,
            'CashierID'       => (int) $this->cashier->ID,
        ];
    }
}
