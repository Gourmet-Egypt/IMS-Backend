<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCommitted;
use App\Models\PurchaseOrderPdf;
use App\Services\PurchaseOrderPdfService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class GeneratePdfsListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Number of times the job may be attempted.
     */
    public $tries = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 1;

    protected $pdfService;

    public function __construct(PurchaseOrderPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function handle(PurchaseOrderCommitted $event)
    {
        $purchaseOrder = $event->purchaseOrder;

        // Idempotency check: skip if PDF already exists
        if (PurchaseOrderPdf::where('purchase_order_id', $purchaseOrder->ID)->exists()) {
            Log::info("PDF already exists for PO #{$purchaseOrder->ID}, skipping generation");
            return;
        }

        $this->pdfService->generatePdf($purchaseOrder);
    }
}
