<?php

namespace App\Services\Commit;

use App\Enums\PurchaseOrderTypeEnum;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderEmail;
use App\Notifications\CommitPurchaseOrderNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends PO notification emails for the rebuilt commit flow. Per-type recipient,
 * perspective, subject, sender, and store-flow decisions come from the strategy's
 * EmailPolicy; the perspective PDF comes from CommitTransferPdfBuilder. The
 * notification itself stays a thin renderer. The legacy PurchaseOrderEmailService
 * and PurchaseOrderNotification are left untouched for the old flow.
 */
class CommitEmailService
{
    public function __construct(
        private StrategyFactory $strategies,
        private CommitTransferPdfBuilder $pdfBuilder,
    ) {}

    public function send(PurchaseOrder $order): void
    {
        $config = DB::table('Configuration')->first();
        if (!$config) {
            return;
        }

        $type = PurchaseOrderTypeEnum::tryFrom((int) $order->POType);
        if (!$type) {
            return;
        }

        // Fall back to the configured store when the PO has none (matches legacy).
        if (empty($order->StoreID) || $order->StoreID == 0) {
            $order->StoreID = $config->StoreID;
        }

        $order->load(['currentStore', 'otherStore']);

        $policy = $this->strategies->for($type, $order)->emailPolicy();

        $recipients = PurchaseOrderEmail::forStores($policy->recipientStoreIds($order, $config))
            ->get()
            ->groupBy('store_id');

        if ($recipients->isEmpty()) {
            return;
        }

        // CC recipients: users flagged receive_all.
        $cc = PurchaseOrderEmail::where('is_active', 1)
            ->where('receive_all', 1)
            ->pluck('email')
            ->toArray();

        [$fromStore, $toStore] = $policy->storeFlow($order);

        foreach ($recipients as $storeId => $records) {
            $perspective = $policy->perspective($order, (int) $storeId);
            $subject     = $this->subjectFor($order, $perspective, $fromStore, $toStore);
            $senderName  = $policy->senderStoreName($order, $perspective);
            $pdf         = $this->pdfBuilder->build($order, $perspective);

            foreach ($records as $record) {
                $mail = Mail::to($record->email);
                if (!empty($cc)) {
                    $mail->cc($cc);
                }

                try {
                    $mail->send(new CommitPurchaseOrderNotification(
                        $order,
                        $pdf,
                        $subject,
                        $senderName,
                        $fromStore,
                        $toStore,
                        $perspective
                    ));
                } catch (\Throwable $e) {
                    Log::error('Failed to send PO email to ' . $record->email . ': ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Perspective-driven subject, shared by all transfer types (default covers
     * supplier). Was PurchaseOrderNotification::getEmailSubject.
     */
    private function subjectFor(PurchaseOrder $order, string $perspective, string $from, string $to): string
    {
        $id = $order->ID;
        $title = $order->title;

        return match ($perspective) {
            'from_store' => "Transfer OUT from {$from} to {$to} - #{$id} - {$title}",
            'to_store'   => "Transfer IN from {$from} to {$to} - #{$id} - {$title}",
            default      => "Purchase Order #{$id} - {$title}",
        };
    }
}
