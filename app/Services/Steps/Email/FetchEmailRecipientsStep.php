<?php

namespace App\Services\Steps\Email;

use App\Models\PurchaseOrderEmail;
use Illuminate\Support\Facades\DB;

class FetchEmailRecipientsStep
{
    public function handle($payload, \Closure $next)
    {
        $configuration = DB::table('Configuration')->first();

        if (!$configuration) {
            $payload->emailRecipients = collect();
            return $next($payload);
        }

        $purchaseOrder = $payload->purchaseOrder;
        $configStoreId = $configuration->StoreID;

        $storeIds = match ((int)$purchaseOrder->POType) {
            2 => [$purchaseOrder->StoreID, $purchaseOrder->OtherStoreID, $configStoreId],
            3 => [$purchaseOrder->StoreID, $purchaseOrder->OtherStoreID],
            default => [$configStoreId],
        };

        $emails = PurchaseOrderEmail::forStores($storeIds)->get();

        $payload->emailRecipients = $emails->groupBy('store_id');

        return $next($payload);
    }
}
