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

        $emails = PurchaseOrderEmail::where('store_id', $configuration->StoreID)
            ->where('is_active', true)
            ->get();

        if ($emails->isEmpty()) {
            Log::warning("No active emails found for store #{$configuration->StoreID}");
        }

        $payload->emailRecipients = $emails;

        return $next($payload);
    }
}
