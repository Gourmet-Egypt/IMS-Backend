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

        $storeIds = match ((int)$purchaseOrder->POType) {
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

        // Log the stores that will receive emails
        $storeList = $payload->emailRecipients->keys()->map(function($storeId) use ($payload) {
            $store = \App\Models\Store::find($storeId);
            return $store ? "#{$storeId} ({$store->Name})" : "#{$storeId}";
        })->join(', ');

        Log::info("Email recipients fetched for Purchase Order #{$purchaseOrder->ID}", [
            'store_count' => $payload->emailRecipients->count(),
            'stores' => $storeList,
            'total_recipients' => $emails->count(),
        ]);

        return $next($payload);
    }
}
