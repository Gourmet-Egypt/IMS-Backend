<?php

namespace App\Jobs;

use App\Services\PurchaseOrderPrintService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PrintDocumentJob implements shouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [30, 60, 120];

    protected string $pdfPath;
    protected int $storeId;
    protected int $copies;

    public function __construct(string $pdfPath, int $storeId, int $copies = 1)
    {
        $this->pdfPath = $pdfPath;
        $this->storeId = $storeId;
        $this->copies = $copies;
    }

    public function handle(PurchaseOrderPrintService $printerService): void
    {
        Log::info("Processing print job", [
            'store_id' => $this->storeId,
            'pdf' => $this->pdfPath,
            'attempt' => $this->attempts(),
        ]);


        if (!File::exists($this->pdfPath)) {
            Log::error("PDF file not found, job will fail permanently", [
                'pdf' => $this->pdfPath,
            ]);
            $this->fail(new \Exception("PDF file not found"));
            return;
        }


        $printerConfig = $printerService->getPrinterConfig($this->storeId);

        if (!$printerConfig) {
            Log::error("No printer configured for store", [
                'store_id' => $this->storeId,
            ]);
            $this->fail(new \Exception("No printer configured"));
            return;
        }


        try {
            $printerService->printPdf($this->pdfPath, $printerConfig, $this->copies);

            Log::info("Print job completed successfully", [
                'store_id' => $this->storeId,
                'printer' => $printerConfig['name'],
            ]);


        } catch (\Exception $e) {
            Log::error("Print job failed", [
                'store_id' => $this->storeId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);


            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Print job failed permanently after all retries", [
            'store_id' => $this->storeId,
            'pdf' => $this->pdfPath,
            'error' => $exception->getMessage(),
        ]);
    }
}
