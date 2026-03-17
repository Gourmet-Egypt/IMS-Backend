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
            $payload->ccRecipients = [];
            return $next($payload);
        }

        $purchaseOrder = $payload->purchaseOrder;
        $configStoreId = $configuration->StoreID;

        $storeIds = match ((int)$purchaseOrder->POType) {
            2, 3 => [$purchaseOrder->StoreID],
            default => [$configStoreId],
        };

        $emails = PurchaseOrderEmail::forStores($storeIds)->get();

        $payload->emailRecipients = $emails->groupBy('store_id');

        // CC recipients: users with receive_all = 1
        $payload->ccRecipients = PurchaseOrderEmail::where('is_active', 1)
            ->where('receive_all', 1)
            ->pluck('email')
            ->toArray();

        return $next($payload);
    }
}
