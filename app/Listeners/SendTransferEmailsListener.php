<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCommitted;
use App\Services\PurchaseOrderEmailService;
use Illuminate\Queue\InteractsWithQueue;

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

        } catch (\Exception $e) {
            // Silently fail or handle error as needed
        }
    }




}
