<?php

namespace App\Services\Steps\Email;

use App\Models\PurchaseOrderEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $storeIds = match ($purchaseOrder->POType) {
            2 => [$purchaseOrder->StoreID, $purchaseOrder->OtherStoreID, $configStoreId],
            3 => [$purchaseOrder->StoreID, $purchaseOrder->OtherStoreID],
            default => [$configStoreId],
        };

        $emails = PurchaseOrderEmail::whereIn('store_id', $storeIds)
            ->where('is_active', true)
            ->get();

        if ($emails->isEmpty()) {
            Log::warning("No active emails found for store #{$configStoreId}");
        }

        $payload->emailRecipients = $emails->groupBy('store_id');

        return $next($payload);
    }
}
