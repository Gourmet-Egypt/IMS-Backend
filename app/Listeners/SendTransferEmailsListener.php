<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCommitted;
use App\Services\PurchaseOrderEmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTransferEmailsListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $emailService;

    public function __construct(PurchaseOrderEmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(PurchaseOrderCommitted $event)
    {
        $purchaseOrder = $event->purchaseOrder;

        $this->emailService->sendNotifications($purchaseOrder);
    }


}
