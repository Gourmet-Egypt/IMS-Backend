<?php

namespace App\Services\Steps\Print;

use App\Models\PurchaseOrderPdf;
use Illuminate\Support\Facades\File;

class FetchPdfPathStep
{
    public function handle($payload, \Closure $next)
    {
        if ($payload->skipPrint) {
            return $next($payload);
        }

        $pdfPath = PurchaseOrderPdf::where('purchase_order_id', $payload->purchaseOrder->ID)
            ->latest()
            ->value('file_path');

        if (!$pdfPath) {
            throw new \Exception("No PDF record found for purchase order: {$payload->purchaseOrder->ID}");
        }

        $fullPath = storage_path('app/'.$pdfPath);

        if (!File::exists($fullPath)) {
            throw new \Exception("PDF file not found at: {$fullPath}");
        }

        $payload->pdfPath = $fullPath;

        return $next($payload);
    }
}
