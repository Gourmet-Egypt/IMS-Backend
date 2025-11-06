<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCommitted;
use App\Services\PurchaseOrderPdfService;
use Illuminate\Contracts\Queue\ShouldQueue;
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
            $pdfs = $this->pdfService->generateAllPdfs($purchaseOrder);

            Log::info("Successfully generated PDFs for Purchase Order #{$purchaseOrder->PONumber}", [
                'pdfs' => array_keys($pdfs)
            ]);
    }

    public function failed(PurchaseOrderCommitted $event, \Throwable $exception)
    {
        Log::error("GeneratePdfsListener failed for Transfer Request #{$event->purchaseOrder->PONumber}: " . $exception->getMessage());
    }



}
