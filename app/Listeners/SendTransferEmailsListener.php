<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCommitted;
use App\Services\PurchaseOrderEmailService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTransferEmailsListener
{
    use InteractsWithQueue;

    protected $emailService;

    public function __construct(PurchaseOrderEmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(PurchaseOrderCommitted $event)
    {
        try {
            $purchaseOrder = $event->purchaseOrder;

            Log::info("SendTransferEmailsListener triggered for Purchase Order", [
                'purchase_order_id' => $purchaseOrder->ID,
                'po_number' => $purchaseOrder->PONumber,
                'po_type' => $purchaseOrder->POType,
            ]);

            $this->emailService->sendNotifications($purchaseOrder);

            Log::info("Successfully completed email notification process for Purchase Order #{$purchaseOrder->PONumber}");

        } catch (\Exception $e) {
            Log::error("Error in SendTransferEmailsListener", [
                'purchase_order_id' => $purchaseOrder->ID ?? 'unknown',
                'po_number' => $purchaseOrder->PONumber ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function failed(PurchaseOrderCommitted $event, \Throwable $exception)
    {
        Log::error("SendTransferEmailsListener failed for Transfer Request #{$event->purchaseOrder->id}: " . $exception->getMessage());
    }




}
