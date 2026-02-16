<?php

namespace App\Services\Steps\Email;

use App\Models\PurchaseOrderPdf;
use Illuminate\Support\Facades\Log;

class FetchPdfsStep
{
    public function handle($payload, \Closure $next)
    {
        $pdfs = PurchaseOrderPdf::where('purchase_order_id', $payload->purchaseOrder->ID)->get();

        if ($pdfs->isEmpty()) {
            Log::warning("No PDFs found for Purchase Order #{$payload->purchaseOrder->ID}");
        } else {
            Log::info("Fetched PDFs for Purchase Order #{$payload->purchaseOrder->ID}", [
                'purchase_order_id' => $payload->purchaseOrder->ID,
                'pdf_count' => $pdfs->count(),
                'pdf_files' => $pdfs->pluck('file_name')->toArray(),
            ]);
        }

        $payload->pdfs = $pdfs;

        return $next($payload);
    }
}
