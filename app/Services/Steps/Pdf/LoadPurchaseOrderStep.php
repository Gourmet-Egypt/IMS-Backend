<?php

namespace App\Services\Steps\Pdf;

class LoadPurchaseOrderStep
{
    public function handle($payload, \Closure $next)
    {
        // If StoreID is 0 or null, use Configuration StoreID (like TransferIN case)
        if (empty($payload->purchaseOrder->StoreID) || $payload->purchaseOrder->StoreID == 0) {
            $configStoreId = \Illuminate\Support\Facades\DB::table('Configuration')->value('StoreID');
            $payload->purchaseOrder->StoreID = $configStoreId;
        }

        $payload->purchaseOrder->load(['condition', 'entries.infos', 'entries.item', 'currentStore', 'otherStore']);

        return $next($payload);
    }
}
