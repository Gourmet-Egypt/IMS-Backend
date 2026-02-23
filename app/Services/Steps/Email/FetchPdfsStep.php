<?php

namespace App\Services\Steps\Email;

use App\Models\PurchaseOrderPdf;

class FetchPdfsStep
{
    public function handle($payload, \Closure $next)
    {
        $pdfs = PurchaseOrderPdf::where('purchase_order_id', $payload->purchaseOrder->ID)->get();

        $payload->pdfs = $pdfs;

        return $next($payload);
    }
}
