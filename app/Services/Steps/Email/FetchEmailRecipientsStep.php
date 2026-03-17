<?php

namespace App\Services\Steps\Email;

use App\Models\PurchaseOrderEmail;

class FetchEmailRecipientsStep
{
    public function handle($payload, \Closure $next)
    {
        $purchaseOrder = $payload->purchaseOrder;

        // Main recipients: users where store_id matches purchase order StoreID
        $payload->emailRecipients = PurchaseOrderEmail::where('is_active', 1)
            ->where('store_id', $purchaseOrder->StoreID)
            ->pluck('email')
            ->toArray();

        // CC recipients: users with receive_all = 1
        $payload->ccRecipients = PurchaseOrderEmail::where('is_active', 1)
            ->where('receive_all', 1)
            ->pluck('email')
            ->toArray();

        return $next($payload);
    }
}
