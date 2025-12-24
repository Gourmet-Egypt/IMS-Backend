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

    public $tries = 12; // Will retry 12 times
    public $backoff = 300; // Wait 300 seconds (5 minutes) between retries

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
            Log::info("Transfer request {$this->transferRequestId} already has PO ID: {$transferRequest->purchase_order_id}");
            return;
        }

        $purchaseOrder = PurchaseOrder::where('PONumber', $this->poNumber)
            ->where('StoreID', $transferRequest->store_id)
            ->first();

        if ($purchaseOrder) {

            $transferRequest->update([
                'purchase_order_id' => $purchaseOrder->ID
            ]);

            Log::info("Successfully synced PO ID {$purchaseOrder->ID} for transfer request {$this->transferRequestId}");
        } else {

            Log::info("PO not found yet for transfer request {$this->transferRequestId}, attempt {$this->attempts()}");

            if ($this->attempts() >= $this->tries) {
                Log::error("Failed to sync PO for transfer request {$this->transferRequestId} after {$this->tries} attempts");
            } else {
                throw new \Exception("Purchase order not yet available in external database");
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SyncPurchaseOrderJob failed for transfer request {$this->transferRequestId}: " . $exception->getMessage());
    }
}
