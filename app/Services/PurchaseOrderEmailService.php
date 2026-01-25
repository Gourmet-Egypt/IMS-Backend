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
