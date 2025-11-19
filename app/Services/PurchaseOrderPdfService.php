<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderPdfService
{
    public function generatePdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['condition', 'entries.infos', 'entries.item', 'currentStore', 'otherStore']);

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'items' => $this->getItemsForPDF($purchaseOrder),
            'condition' => $purchaseOrder->condition
        ];

        $pdf = Pdf::loadView('pdfs.purchase_order', $data);
        $filePath = $this->savePdf($pdf, $purchaseOrder->PONumber);

        PurchaseOrderPdf::create([
            'purchase_order_id' => $purchaseOrder->ID,
            'file_path' => $filePath,
            'file_name' => basename($filePath)
        ]);

        Log::info("PDF generated successfully for Purchase Order #{$purchaseOrder->PONumber}");

        return $filePath;
    }

    protected function getItemsForPDF(PurchaseOrder $purchaseOrder)
    {
        $items = [];

        foreach ($purchaseOrder->entries as $entry) {
            if ($entry->infos->isEmpty()) {
                $items[] = [
                    'lookupcode' => $entry->item->ItemLookupCode ?? '',
                    'description' => $entry->ItemDescription,
                    'quantity_requested' => $entry->QuantityOrdered,
                    'quantity_received' => $entry->QuantityReceived,
                    'production_date' => null,
                    'expire_date' => null,
                    'quantity_issued' => 0,
                    'sn' => null,
                ];
            } else {
                foreach ($entry->infos as $info) {
                    $items[] = [
                        'lookupcode' => $entry->item->ItemLookupCode ?? '',
                        'description' => $entry->ItemDescription,
                        'quantity_requested' => $entry->QuantityOrdered,
                        'quantity_received' => $entry->QuantityReceived,
                        'production_date' => $info->production_date,
                        'expire_date' => $info->expire_date,
                        'quantity_issued' => $info->quantity_issued,
                        'sn' => $info->SN,
                    ];
                }
            }
        }

        return collect($items)->map(function ($item) {
            return (object) $item;
        });
    }

    protected function savePdf($pdf, $purchaseOrderNumber)
    {
        $fileName = "purchase_order_{$purchaseOrderNumber}_".time().".pdf";
        $path = "pdfs/purchase_order/{$purchaseOrderNumber}/{$fileName}";
        $directory = dirname($path);

        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory, 0775, true);
        }

        Storage::put($path, $pdf->output());

        return $path;
    }

    public function getPdfsForPurchaseOrder($purchaseOrderId)
    {
        return PurchaseOrderPdf::where('purchase_order_id', $purchaseOrderId)->get();
    }

    public function deletePdfsForPurchaseOrder($purchaseOrderId)
    {
        $pdfs = PurchaseOrderPdf::where('purchase_order_id', $purchaseOrderId)->get();

        foreach ($pdfs as $pdf) {
            if (Storage::exists($pdf->file_path)) {
                Storage::delete($pdf->file_path);
            }
            $pdf->delete();
        }
    }
}
