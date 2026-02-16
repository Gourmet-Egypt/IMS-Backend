<?php

namespace App\Services\Steps\Pdf;

use App\Models\PurchaseOrderPdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SavePdfStep
{
    public function handle($payload, \Closure $next)
    {
        $fileName = "transfer_request_{$payload->purchaseOrder->PONumber}_".time().".pdf";
        $path = "pdfs/purchase_order/{$payload->purchaseOrder->PONumber}/{$fileName}";
        $directory = dirname($path);

        Log::info("Starting PDF generation for Purchase Order #{$payload->purchaseOrder->PONumber}", [
            'purchase_order_id' => $payload->purchaseOrder->ID,
            'file_name' => $fileName,
            'path' => $path,
        ]);

        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory, 0775, true);
            Log::info("Created directory for PDF storage: {$directory}");
        }

        Storage::put($path, $payload->pdf->output());

        $pdfRecord = PurchaseOrderPdf::create([
            'purchase_order_id' => $payload->purchaseOrder->ID,
            'file_path' => $path,
            'file_name' => basename($path)
        ]);

        Log::info("PDF saved successfully for Purchase Order #{$payload->purchaseOrder->PONumber}", [
            'purchase_order_id' => $payload->purchaseOrder->ID,
            'pdf_id' => $pdfRecord->id,
            'file_path' => $path,
            'file_size' => Storage::size($path) . ' bytes',
        ]);

        $payload->filePath = $path;

        return $next($payload);
    }
}
