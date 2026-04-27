<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Support\Pipeline;
use App\Services\Steps\Print\FetchPdfPathStep;
use App\Services\Steps\Print\PrintToNetworkStep;
use App\Services\Steps\Print\ValidatePrinterConfigStep;

class PurchaseOrderPrintService
{
    protected Pipeline $pipeline;

    public function __construct()
    {
        $this->pipeline = new Pipeline();
    }

    public function printPdf(PurchaseOrder $purchaseOrder, int $copies = 1)
    {
        $payload = (object) [
            'purchaseOrder' => $purchaseOrder,
            'printerConfig' => null,
            'pdfPath' => null,
            'copies' => $copies,
            'skipPrint' => false,
        ];

        $this->pipeline
            ->send($payload)
            ->through([
                ValidatePrinterConfigStep::class,
                FetchPdfPathStep::class,
                PrintToNetworkStep::class,
            ])
            ->thenReturn();
    }

}
