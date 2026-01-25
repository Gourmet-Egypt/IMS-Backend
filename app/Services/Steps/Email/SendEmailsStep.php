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

        foreach ($payload->emailRecipients as $emailRecord) {
            Mail::to($emailRecord->email)
                ->send(new PurchaseOrderNotification($payload->purchaseOrder, $payload->pdfs));

            $payload->sentCount++;
            Log::info("Email sent to {$emailRecord->email} for Purchase Order #{$payload->purchaseOrder->ID}");
        }

        Log::info("Sent {$payload->sentCount} emails for Purchase Order #{$payload->purchaseOrder->ID}");

        return $next($payload);
    }
}
