<?php

namespace App\Jobs\Commit;

use App\Models\PurchaseOrder;
use App\Services\Commit\CommitPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * First link of the post-commit saga: generate and store the PO PDF.
 * The email and print jobs that follow depend on this having run.
 */
class GeneratePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public PurchaseOrder $order) {}

    public function handle(CommitPdfService $pdf): void
    {
        $pdf->generate($this->order);
    }
}
