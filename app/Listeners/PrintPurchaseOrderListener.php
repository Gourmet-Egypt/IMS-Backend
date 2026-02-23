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

        try {
            $this->printerService->printPdf($purchaseOrder, 1);
        } catch (\Exception $e) {
            // Silently fail or handle error as needed
        }
    }
}
