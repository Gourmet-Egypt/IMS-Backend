<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderPdf;
use App\Support\Pipeline;
use App\Services\Steps\Pdf\GeneratePdfStep;
use App\Services\Steps\Pdf\LoadPurchaseOrderStep;
use App\Services\Steps\Pdf\SavePdfStep;
use App\Services\Steps\Pdf\TransformItemsStep;

class PurchaseOrderPdfService
{
    protected Pipeline $pipeline;

    public function __construct()
    {
        $this->pipeline = new Pipeline();
    }

    public function generatePdf(PurchaseOrder $purchaseOrder)
    {
        $payload = (object) [
            'purchaseOrder' => $purchaseOrder,
            'items' => null,
            'pdf' => null,
            'filePath' => null,
        ];

        $result = $this->pipeline
            ->send($payload)
            ->through([
                LoadPurchaseOrderStep::class,
                TransformItemsStep::class,
                GeneratePdfStep::class,
                SavePdfStep::class,
            ])
            ->thenReturn();

        return $result->filePath;
    }

}
