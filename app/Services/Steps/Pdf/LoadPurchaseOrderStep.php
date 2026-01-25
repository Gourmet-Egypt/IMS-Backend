<?php

namespace App\Services\Steps\Pdf;

class LoadPurchaseOrderStep
{
    public function handle($payload, \Closure $next)
    {
        $payload->purchaseOrder->load(['condition', 'entries.infos', 'entries.item', 'currentStore', 'otherStore']);

        return $next($payload);
    }
}
