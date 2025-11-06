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

            $this->emailService->sendNotifications($purchaseOrder);

            Log::info("Successfully sent email notifications for Purchase Order #{$purchaseOrder->PONumber}");

        } catch (\Exception $e) {
            Log::error("Error in SendTransferEmailsListener: " . $e->getMessage());

        }
    }

    public function failed(PurchaseOrderCommitted $event, \Throwable $exception)
    {
        Log::error("SendTransferEmailsListener failed for Transfer Request #{$event->purchaseOrder->id}: " . $exception->getMessage());
    }




}
