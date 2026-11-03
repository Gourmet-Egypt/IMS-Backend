<?php

namespace App\Services\Commit;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderPdf;
use Illuminate\Support\Facades\File;

/**
 * Prints the stored PO PDF for the rebuilt commit flow by streaming it to the
 * configured network printer. Replaces the legacy PurchaseOrderPrintService for
 * the new flow (that service is left untouched for the old flow). Printing is a
 * best-effort side effect: config gaps and socket errors are skipped quietly.
 */
class CommitPrintService
{
    public function print(PurchaseOrder $order, int $copies = 1): void
    {
        if (!config('printing.enabled')) {
            return;
        }

        $printerConfig = config('printing.printers');
        if (!$printerConfig) {
            return;
        }

        $pdfPath = PurchaseOrderPdf::where('purchase_order_id', $order->ID)
            ->latest()
            ->value('file_path');

        if (!$pdfPath) {
            throw new \Exception("No PDF record found for purchase order: {$order->ID}");
        }

        $fullPath = storage_path('app/' . $pdfPath);
        if (!File::exists($fullPath)) {
            throw new \Exception("PDF file not found at: {$fullPath}");
        }

        $ip   = $printerConfig['ip'];
        $port = $printerConfig['port'] ?? 9100;

        try {
            $pdfContent = File::get($fullPath);

            $socket = @fsockopen($ip, $port, $errno, $errstr, 10);
            if (!$socket) {
                return;
            }

            for ($i = 0; $i < $copies; $i++) {
                $written = fwrite($socket, $pdfContent);
                if ($written === false) {
                    fclose($socket);
                    return;
                }

                if ($i < $copies - 1) {
                    usleep(500000); // 0.5s between copies
                }
            }

            fclose($socket);
        } catch (\Exception $e) {
            // Best-effort: skip printing on any error.
            return;
        }
    }
}
