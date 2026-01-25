<?php

namespace App\Services\Steps\Pdf;

use App\Models\PurchaseOrderPdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SavePdfStep
{
    public function handle($payload, \Closure $next)
    {
        $fileName = "purchase_order_{$payload->purchaseOrder->PONumber}_".time().".pdf";
        $path = "pdfs/purchase_order/{$payload->purchaseOrder->PONumber}/{$fileName}";
        $directory = dirname($path);

        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory, 0775, true);
        }

        Storage::put($path, $payload->pdf->output());

        PurchaseOrderPdf::create([
            'purchase_order_id' => $payload->purchaseOrder->ID,
            'file_path' => $path,
            'file_name' => basename($path)
        ]);

        Log::info("PDF generated successfully for Purchase Order #{$payload->purchaseOrder->PONumber}");

        $payload->filePath = $path;

        return $next($payload);
    }
}
