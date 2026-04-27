<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCommitted;
use App\Services\PurchaseOrderPrintService;
use Illuminate\Queue\InteractsWithQueue;

class PrintPurchaseOrderListener
{
    use InteractsWithQueue;

    protected $printerService;

    public function __construct(PurchaseOrderPrintService $printerService)
    {
        $this->printerService = $printerService;
    }

    public function handle(PurchaseOrderCommitted $event)
    {
        $purchaseOrder = $event->purchaseOrder;

        $this->printerService->printPdf($purchaseOrder, 1);
    }
}
