<?php

namespace App\Jobs;

use App\Models\PurchaseOrder;
use App\Models\TransferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPurchaseOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 200;
    public $backoff = 180;

    protected $transferRequestId;
    protected $poNumber;

    /**
     * Create a new job instance.
     */
    public function __construct(int $transferRequestId, string $poNumber)
    {
        $this->transferRequestId = $transferRequestId;
        $this->poNumber = $poNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $transferRequest = TransferRequest::find($this->transferRequestId);

        if (!$transferRequest) {
            Log::warning("Transfer request {$this->transferRequestId} not found");
            return;
        }

        if ($transferRequest->purchase_order_id) {
            return;
        }


        $purchaseOrder = PurchaseOrder::where('PONumber', $this->poNumber)
            ->first();

        if ($purchaseOrder) {
            $transferRequest->update([
                'purchase_order_id' => $purchaseOrder->ID
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("❌ SyncPurchaseOrderJob permanently failed for transfer request {$this->transferRequestId}: " . $exception->getMessage());
    }
}
