<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use App\Models\TransferRequest;
use App\Services\PurchaseOrderTracker;

class PurchaseOrderObserver
{
    public function __construct(
        private PurchaseOrderTracker $purchaseOrderTracker
    ) {
    }

    /**
     * Handle the PurchaseOrder "created" event.
     */
    public function created(PurchaseOrder $purchaseOrder): void
    {

        if (!$this->purchaseOrderTracker->isExpected($purchaseOrder->purchase_order_number)) {
            return;
        }

        $transferRequestId = $this->purchaseOrderTracker->getTransferRequestId($purchaseOrder->purchase_order_number);

        if ($transferRequestId) {
            $this->updateTransferRequest($purchaseOrder, $transferRequestId);


            $this->purchaseOrderTracker->forget($purchaseOrder->purchase_order_number);
        }
    }

    /**
     * Update transfer request with the actual purchase order ID
     */
    private function updateTransferRequest(PurchaseOrder $purchaseOrder, int $transferRequestId): void
    {
        $transferRequest = TransferRequest::find($transferRequestId);

        if (!$transferRequest) {
            return;
        }

        $transferRequest->update([
            'purchase_order_id' => $purchaseOrder->id,
        ]);

    }
}
