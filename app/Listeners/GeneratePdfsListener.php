<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCommitted;
use App\Services\PurchaseOrderPdfService;
use Illuminate\Queue\InteractsWithQueue;

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

        $this->pdfService->generatePdf($purchaseOrder);
    }


}
