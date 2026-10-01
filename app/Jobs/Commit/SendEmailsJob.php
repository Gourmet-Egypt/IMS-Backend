<?php

namespace App\Jobs\Commit;

use App\Models\PurchaseOrder;
use App\Services\Commit\CommitEmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Second link of the post-commit saga: send the PO notification emails.
 * Runs only after the PDF job succeeds (it attaches the generated PDF).
 */
class SendEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public PurchaseOrder $order) {}

    public function handle(CommitEmailService $emails): void
    {
        $emails->send($this->order);
    }
}
