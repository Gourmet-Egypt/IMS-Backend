<?php

namespace App\Jobs\Commit;

use App\Models\PurchaseOrder;
use App\Services\Commit\CommitPrintService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Final link of the post-commit saga: print the PO PDF.
 * Runs only after the PDF job succeeds.
 */
class PrintJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public PurchaseOrder $order) {}

    public function handle(CommitPrintService $printer): void
    {
        $printer->print($this->order, 1);
    }
}
