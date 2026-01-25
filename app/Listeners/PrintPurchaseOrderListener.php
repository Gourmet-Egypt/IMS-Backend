<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCommitted;
use App\Services\PurchaseOrderPrintService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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
            Log::error("Failed to print Purchase Order #{$purchaseOrder->PONumber}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function failed(PurchaseOrderCommitted $event, \Throwable $exception)
    {
        Log::error("PrintPurchaseOrderListener failed for Purchase Order #{$event->purchaseOrder->PONumber}: ".$exception->getMessage(),
            [
                'po_number' => $event->purchaseOrder->PONumber,
                'store_id' => $event->purchaseOrder->store_id ?? null,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
    }
}
