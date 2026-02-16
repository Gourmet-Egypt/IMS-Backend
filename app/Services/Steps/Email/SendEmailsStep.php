<?php

namespace App\Services\Steps\Email;

use App\Notifications\PurchaseOrderNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailsStep
{
    public function handle($payload, \Closure $next)
    {
        if ($payload->emailRecipients->isEmpty()) {
            Log::info("No email recipients found for Purchase Order #{$payload->purchaseOrder->ID}");
            return $next($payload);
        }

        $purchaseOrder = $payload->purchaseOrder;
        $isTransfer = in_array((int)$purchaseOrder->POType, [2, 3]);

        // Log start of email sending process
        Log::info("Starting email sending process for Purchase Order #{$purchaseOrder->ID}", [
            'po_number' => $purchaseOrder->PONumber,
            'po_type' => $purchaseOrder->POType,
            'store_count' => $payload->emailRecipients->count(),
        ]);

        $storesSummary = [];

        foreach ($payload->emailRecipients as $storeId => $recipients) {
            $recipientStoreId = $storeId;
            $store = \App\Models\Store::find($storeId);
            $storeName = $store ? $store->Name : "Unknown";
            $storeEmails = [];

            foreach ($recipients as $emailRecord) {
                $perspective = $this->determinePerspective($purchaseOrder, $recipientStoreId, $isTransfer);

                // PDF will be generated on-demand in the notification based on perspective
                Mail::to($emailRecord->email)
                    ->send(new PurchaseOrderNotification(
                        $purchaseOrder,
                        $payload->pdfs,
                        $perspective
                    ));

                $payload->sentCount++;
                $storeEmails[] = $emailRecord->email;
                Log::info("Email sent to {$emailRecord->email} (Store #{$recipientStoreId}, {$perspective}) for Purchase Order #{$purchaseOrder->ID}");
            }

            $storesSummary[] = [
                'store_id' => $storeId,
                'store_name' => $storeName,
                'recipient_count' => count($storeEmails),
                'recipients' => $storeEmails,
            ];
        }

        Log::info("Completed email sending for Purchase Order #{$purchaseOrder->ID}", [
            'total_emails_sent' => $payload->sentCount,
            'stores_notified' => $storesSummary,
        ]);

        return $next($payload);
    }

    private function determinePerspective($purchaseOrder, $recipientStoreId, $isTransfer)
    {
        if (!$isTransfer) {
            return 'default';
        }

        // Get the actual Store IDs from the relationships for comparison
        // since recipientStoreId comes from email table which uses Store.ID
        $currentStoreModelId = $purchaseOrder->currentStore->ID ?? null;
        $otherStoreModelId = $purchaseOrder->otherStore->ID ?? null;

        $recipientStoreId = (int) $recipientStoreId;

        $isCreatorStore = $currentStoreModelId && $recipientStoreId === (int) $currentStoreModelId;
        $isOtherStore = $otherStoreModelId && $recipientStoreId === (int) $otherStoreModelId;

        if ((int)$purchaseOrder->POType == 2) {
            // POType 2: goods flow from OtherStore to CurrentStore
            if ($isOtherStore) {
                return 'from_store';  // OtherStore sees this as Transfer OUT
            }
            return 'to_store';  // CurrentStore sees this as Transfer IN
        }

        if ((int)$purchaseOrder->POType == 3) {
            // POType 3: goods flow from CurrentStore to OtherStore
            return $isCreatorStore ? 'from_store' : 'to_store';
        }

        return 'default';
    }
}
