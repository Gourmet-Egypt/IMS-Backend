<?php


namespace App\Services;

use App\Enums\PurchaseOrderEnum;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PurchaseOrderPdfService
{
    public function generateAllPdfs(PurchaseOrder $purchaseOrder)
    {
        $pdfs = [];

        $pdfs['main'] = $this->generateMainPdf($purchaseOrder);

        $pdfs['info'] = $this->generateInfoPdf($purchaseOrder);

        $pdfs['condition'] = $this->generateConditionPdf($purchaseOrder);

        foreach ($pdfs as $type => $pdfPath) {
            PurchaseOrderPdf::create([
                'purchase_order_id' => $purchaseOrder->ID,
                'pdf_type' => $type,
                'file_path' => $pdfPath,
                'file_name' => basename($pdfPath)
            ]);
        }



        Log::info("PDFs generated successfully for Purchase Order #{$purchaseOrder->PONumber}");

        return $pdfs;


    }

    protected function generateMainPdf(PurchaseOrder $purchaseOrder)
    {
        $pdf = Pdf::loadView('pdfs.purchase_order_main', [
            'purchaseOrder' => $purchaseOrder
        ]);

        return $this->savePdf($pdf, $purchaseOrder->PONumber, PurchaseOrderEnum::MAIN->value);
    }

    protected function generateInfoPdf(PurchaseOrder $purchaseOrder)
    {
        $pdf = Pdf::loadView('pdfs.purchase_order_info', [
            'purchaseOrder' => $purchaseOrder,
            'items' => $purchaseOrder->entries
        ]);

        return $this->savePdf($pdf, $purchaseOrder->PONumber, PurchaseOrderEnum::INFO->value);
    }

    protected function generateConditionPdf(PurchaseOrder $purchaseOrder)
    {

        $pdf = Pdf::loadView('pdfs.purchase_order_condition', [
            'purchaseOrder' => $purchaseOrder,
            'condition' => $purchaseOrder->condition
        ]);

        return $this->savePdf($pdf, $purchaseOrder->PONumber, PurchaseOrderEnum::CONDITION->value);
    }

    protected function savePdf($pdf, $purchaseOrderNumber, $type)
    {
        $fileName = "purchase_order_{$type}_{$purchaseOrderNumber}_" . ".pdf";

        $path = "pdfs/purchase_order/{$purchaseOrderNumber}/{$fileName}";
        $directory = dirname($path);

        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory, 0775, true);
        }

        Storage::put($path, $pdf->output());

        return $path;

    }

    public function getPdfsForTransferRequest($purchaseOrderId)
    {
        return PurchaseOrderPdf::where('purchase_order_id', $purchaseOrderId)->get();
    }

    public function deletePdfsForTransferRequest($purchaseOrderId)
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
