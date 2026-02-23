<?php

namespace App\Services\Steps\Email;

use App\Notifications\PurchaseOrderNotification;
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
            }
        }

        return $next($payload);
    }

    private function determinePerspective($purchaseOrder, $recipientStoreId, $isTransfer)
    {
        if (!$isTransfer) {
            return 'default';
        }

        $currentStoreModelId = $purchaseOrder->currentStore->ID ?? null;
        $otherStoreModelId = $purchaseOrder->otherStore->ID ?? null;

        $recipientStoreId = (int) $recipientStoreId;

        $isCurrentStore = $currentStoreModelId && $recipientStoreId === (int) $currentStoreModelId;
        $isOtherStore = $otherStoreModelId && $recipientStoreId === (int) $otherStoreModelId;

        // Flow is ALWAYS: FROM CurrentStore TO OtherStore
        // POType only determines the label (IN vs OUT)

        if ((int)$purchaseOrder->POType == 2) {
            // POType 2: Label is "Transfer IN" for CurrentStore
            if ($isCurrentStore) {
                return 'to_store';  // CurrentStore: "Transfer IN" from CurrentStore to OtherStore
            }
            if ($isOtherStore) {
                return 'from_store';  // OtherStore: "Transfer OUT" from CurrentStore to OtherStore
            }
            // Global users: see from CurrentStore perspective
            return 'to_store';
        }

        if ((int)$purchaseOrder->POType == 3) {
            // POType 3: Label is "Transfer OUT" for CurrentStore
            if ($isCurrentStore) {
                return 'from_store';  // CurrentStore: "Transfer OUT" from CurrentStore to OtherStore
            }
            if ($isOtherStore) {
                return 'to_store';  // OtherStore: "Transfer IN" from CurrentStore to OtherStore
            }
            // Global users: see from CurrentStore perspective
            return 'from_store';
        }

        return 'default';
    }
}
