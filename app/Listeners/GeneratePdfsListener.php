<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCommitted;
use App\Services\PurchaseOrderPdfService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GeneratePdfsListener implements ShouldQueue
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

        $this->pdfService->generatePdf($purchaseOrder);
    }
}
