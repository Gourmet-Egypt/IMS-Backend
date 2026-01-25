<?php

namespace App\Services\Steps\Pdf;

use Barryvdh\DomPDF\Facade\Pdf;

class GeneratePdfStep
{
    public function handle($payload, \Closure $next)
    {
        $data = [
            'purchaseOrder' => $payload->purchaseOrder,
            'items' => $payload->items,
            'condition' => $payload->purchaseOrder->condition
        ];

        $payload->pdf = Pdf::loadView('pdfs.purchase_order', $data);

        return $next($payload);
    }
}
