<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCommitted;
use App\Models\PurchaseOrderPdf;
use App\Services\PurchaseOrderPdfService;
use App\Services\PurchaseOrderPrintService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PrintPurchaseOrderListener
{
    use InteractsWithQueue;

    protected $printerService;
    protected $pdfService;

    public function __construct(PurchaseOrderPrintService $printerService, PurchaseOrderPdfService $pdfService)
    {
        $this->printerService = $printerService;
        $this->pdfService = $pdfService;
    }

    public function handle(PurchaseOrderCommitted $event)
    {
        $purchaseOrder = $event->purchaseOrder;

        if (!config('printing.enabled')) {
            Log::info("Auto-print disabled, skipping print for PO #{$purchaseOrder->PONumber}");
            return;
        }

        try {
            $pdfPath = PurchaseOrderPdf::where('purchase_order_id', $purchaseOrder->ID)->value('file_path');
            $pdfPath = storage_path('app/'.$pdfPath);

            if (!File::exists($pdfPath)) {
                throw new \Exception("PDF file not found at: {$pdfPath}");
            }


            $storeId = $purchaseOrder->StoreID ?? 1;


            $queued = $this->printerService->printPdf($pdfPath, config('printing.printers'), 1);

            if ($queued) {
                Log::info("Successfully queued print job for Purchase Order #{$purchaseOrder->PONumber}", [
                    'pdf' => $pdfPath,
                    'store_id' => $storeId,
                    'po_number' => $purchaseOrder->PONumber,
                ]);
            } else {
                Log::warning("Print job not queued for Purchase Order #{$purchaseOrder->PONumber}", [
                    'store_id' => $storeId,
                    'reason' => 'No printer configured or auto-print disabled'
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Failed to queue print job for Purchase Order #{$purchaseOrder->PONumber}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

        }
    }

    public function failed(PurchaseOrderCommitted $event, \Throwable $exception)
    {
        Log::error("PrintPurchaseOrderListener failed for Purchase Order #{$event->purchaseOrder->PONumber}: ".$exception->getMessage(),
            [
                'po_number' => $event->purchaseOrder->PONumber,
                'store_id' => $event->purchaseOrder->store_id ?? null,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
    }
}
