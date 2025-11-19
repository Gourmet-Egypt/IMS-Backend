<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCommitted;
use App\Services\PurchaseOrderPdfService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class GeneratePdfsListener
{
    use InteractsWithQueue;

    protected $pdfService;

    public function __construct(PurchaseOrderPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function handle(PurchaseOrderCommitted $event)
    {
        $purchaseOrder = $event->purchaseOrder;
        $pdf = $this->pdfService->generatePdf($purchaseOrder);

        Log::info("Successfully generated PDF for Purchase Order #{$purchaseOrder->PONumber}", [
            'pdf' => $pdf
        ]);
    }

    public function failed(PurchaseOrderCommitted $event, \Throwable $exception)
    {
        Log::error("GeneratePdfsListener failed for Transfer Request #{$event->purchaseOrder->PONumber}: ".$exception->getMessage());
    }


}
