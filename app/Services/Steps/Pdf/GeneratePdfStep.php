<?php

namespace App\Services\Steps\Pdf;

use Barryvdh\DomPDF\Facade\Pdf;

class GeneratePdfStep
{
    public function handle($payload, \Closure $next)
    {
        // Generate a default PDF for printing/storage purposes only
        $data = [
            'purchaseOrder' => $payload->purchaseOrder,
            'items' => $payload->items,
            'condition' => $payload->purchaseOrder->condition,
            'perspective' => 'default'
        ];

        // Get paper size from config (A4 or A5)
        $paperSize = config('app.pdf_paper_size', 'A4');
        $viewName = $paperSize === 'A5' ? 'pdfs.purchase_order_A5' : 'pdfs.purchase_order';

        $payload->pdf = Pdf::loadView($viewName, $data);

        return $next($payload);
    }
}
