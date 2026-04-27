<?php

namespace App\Services\Steps\Pdf;

use Barryvdh\DomPDF\Facade\Pdf;

class GeneratePdfStep
{
    public function handle($payload, \Closure $next)
    {
        $perspective = match ($payload->purchaseOrder->POType) {
            '2', '4' => 'to_store',
            '3', '5' => 'from_store',
        };

        $data = [
            'purchaseOrder' => $payload->purchaseOrder,
            'items' => $payload->items,
            'condition' => $payload->purchaseOrder->condition,
            'perspective' => $perspective,
        ];

        // Get paper size from config (A4, A5)
        $paperSize = strtoupper(config('app.pdf_paper_size', 'A4'));

        $viewName = $paperSize === 'A5' ? 'pdfs.purchase_order_A5' : 'pdfs.purchase_order';

        $pdf = Pdf::loadView($viewName, $data);

        // Set paper size - always A4 for printer compatibility
        // A5 template uses A4 with content in top half (cut in half after printing)
        $pdf->setPaper('a4', 'portrait');

        $payload->pdf = $pdf;

        return $next($payload);
    }
}
