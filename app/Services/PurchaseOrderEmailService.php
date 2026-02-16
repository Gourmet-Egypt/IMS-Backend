<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderPdf;
use App\Notifications\PurchaseOrderNotification;
use App\Support\Pipeline;
use App\Services\Steps\Email\FetchEmailRecipientsStep;
use App\Services\Steps\Email\FetchPdfsStep;
use App\Services\Steps\Email\SendEmailsStep;
use Illuminate\Support\Facades\Mail;

class PurchaseOrderEmailService
{
    protected Pipeline $pipeline;

    public function __construct()
    {
        $this->pipeline = new Pipeline();
    }

    public function sendNotifications(PurchaseOrder $purchaseOrder)
    {
        // If StoreID is 0 or null, use Configuration StoreID (like TransferIN case)
        if (empty($purchaseOrder->StoreID) || $purchaseOrder->StoreID == 0) {
            $configStoreId = \Illuminate\Support\Facades\DB::table('Configuration')->value('StoreID');
            $purchaseOrder->StoreID = $configStoreId;
        }

        // Load store relationships to prevent "Unknown" store names
        $purchaseOrder->load(['currentStore', 'otherStore']);

        // Log store information for debugging
        \Illuminate\Support\Facades\Log::info("Sending notifications for PO #{$purchaseOrder->ID}", [
            'PONumber' => $purchaseOrder->PONumber,
            'POType' => $purchaseOrder->POType,
            'StoreID' => $purchaseOrder->StoreID,
            'OtherStoreID' => $purchaseOrder->OtherStoreID,
            'currentStore' => $purchaseOrder->currentStore ? $purchaseOrder->currentStore->Name : 'NULL',
            'currentStore_ID' => $purchaseOrder->currentStore ? $purchaseOrder->currentStore->ID : 'NULL',
            'otherStore' => $purchaseOrder->otherStore ? $purchaseOrder->otherStore->Name : 'NULL',
            'otherStore_ID' => $purchaseOrder->otherStore ? $purchaseOrder->otherStore->ID : 'NULL',
        ]);

        $payload = (object) [
            'purchaseOrder' => $purchaseOrder,
            'emailRecipients' => null,
            'pdfs' => null,
            'sentCount' => 0,
        ];

        $this->pipeline
            ->send($payload)
            ->through([
                FetchEmailRecipientsStep::class,
                FetchPdfsStep::class,
                SendEmailsStep::class,
            ])
            ->thenReturn();
    }

}
