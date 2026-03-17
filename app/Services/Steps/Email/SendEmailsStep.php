<?php

namespace App\Services\Steps\Email;

use App\Notifications\PurchaseOrderNotification;
use Illuminate\Support\Facades\Mail;

class SendEmailsStep
{
    public function handle($payload, \Closure $next)
    {
        if (empty($payload->emailRecipients)) {
            return $next($payload);
        }

        $purchaseOrder = $payload->purchaseOrder;
        $ccRecipients = $payload->ccRecipients ?? [];

        foreach ($payload->emailRecipients as $email) {
            $mail = Mail::to($email);

            // Add CC recipients (receive_all = 1 users)
            if (!empty($ccRecipients)) {
                $mail->cc($ccRecipients);
            }

            $mail->send(new PurchaseOrderNotification(
                $purchaseOrder,
                $payload->pdfs,
                'default'
            ));

            $payload->sentCount++;
        }

        return $next($payload);
    }

}
