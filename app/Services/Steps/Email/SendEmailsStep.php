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
            return $next($payload);
        }

        $purchaseOrder = $payload->purchaseOrder;
        $isTransfer = in_array((int)$purchaseOrder->POType, [2, 3]);

        foreach ($payload->emailRecipients as $storeId => $recipients) {
            $recipientStoreId = $storeId;

            foreach ($recipients as $emailRecord) {
                $perspective = $this->determinePerspective($purchaseOrder, $recipientStoreId, $isTransfer);

                Mail::to($emailRecord->email)
                    ->send(new PurchaseOrderNotification(
                        $purchaseOrder,
                        $payload->pdfs,
                        $perspective
                    ));

                $payload->sentCount++;
                Log::info("Email sent to {$emailRecord->email} (Store #{$recipientStoreId}, {$perspective}) for Purchase Order #{$purchaseOrder->ID}");
            }
        }

        Log::info("Sent {$payload->sentCount} emails for Purchase Order #{$purchaseOrder->ID}");

        return $next($payload);
    }

    private function determinePerspective($purchaseOrder, $recipientStoreId, $isTransfer)
    {
        if (!$isTransfer) {
            return 'default';
        }

        $isCreatorStore = $recipientStoreId == $purchaseOrder->StoreID;
        $isOtherStore = $recipientStoreId == $purchaseOrder->OtherStoreID;

        if ((int)$purchaseOrder->POType == 2) {
            if ($isOtherStore) {
                return 'from_store';
            }
            return 'to_store';
        }

        if ((int)$purchaseOrder->POType == 3) {
            return $isCreatorStore ? 'from_store' : 'to_store';
        }

        return 'default';
    }
}
