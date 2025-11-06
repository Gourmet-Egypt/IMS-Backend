<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderEmail;
use App\Models\PurchaseOrderPdf;
use App\Notifications\PurchaseOrderNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PurchaseOrderEmailService
{
    public function sendNotifications(PurchaseOrder $purchaseOrder)
    {
        $emails = PurchaseOrderEmail::where('store_id', $purchaseOrder->StoreID)
            ->where('is_active', true)
            ->get();


        if ($emails->isEmpty()) {
            Log::warning("No active emails found for store #{$purchaseOrder->StoreID}");
            return;
        }

        $pdfs = PurchaseOrderPDF::where('purchase_order_id', $purchaseOrder->ID)->get();

        if ($pdfs->isEmpty()) {
            Log::warning("No PDFs found for Purchase Order #{$purchaseOrder->ID}");
        }

        $sentCount = 0;
        foreach ($emails as $emailRecord) {

            Mail::to($emailRecord->email)
                ->send(new PurchaseOrderNotification($purchaseOrder, $pdfs));

            $sentCount++;
            Log::info("Email sent to {$emailRecord->email} for Purchase Order #{$purchaseOrder->ID}");

        }

        Log::info("Sent {$sentCount} emails for Purchase Order #{$purchaseOrder->ID}");


    }

    /**
     * Send email to a specific recipient
     */
    public function sendToEmail($email, PurchaseOrder $purchaseOrder)
    {
        $pdfs = PurchaseOrderPdf::where('purchase_order_id', $purchaseOrder->ID)->get();

        Mail::to($email)
            ->send(new PurchaseOrderNotification($purchaseOrder, $pdfs));
    }

    /**
     * Resend notifications for a transfer request
     */
    public function resendNotifications($purchaseOrderId)
    {
        $purchaseOrder = PurchaseOrder::with(['entries.infos', 'condition', 'otherStore', 'currentStore'])
            ->findOrFail($purchaseOrderId);

        $this->sendNotifications($purchaseOrderId);
    }
}
