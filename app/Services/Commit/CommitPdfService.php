<?php

namespace App\Services\Commit;

use App\Enums\PurchaseOrderTypeEnum;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderPdf;
use Illuminate\Support\Facades\Storage;

/**
 * Generates and stores the canonical PO PDF for the rebuilt commit flow, and
 * records it so the print step can find it. Uses the new commit blades via
 * CommitTransferPdfBuilder. Replaces the legacy PurchaseOrderPdfService for the
 * new flow (that service is left untouched for the old flow).
 */
class CommitPdfService
{
    public function __construct(private CommitTransferPdfBuilder $builder) {}

    /**
     * Build the stored/printed PDF (one canonical perspective per PO), save it,
     * record it, and return the storage path.
     */
    public function generate(PurchaseOrder $order): string
    {
        $perspective = $this->canonicalPerspective($order);
        $pdf = $this->builder->build($order, $perspective);

        $fileName  = $this->filePrefix($perspective) . "_{$order->PONumber}_" . time() . ".pdf";
        $path      = "pdfs/purchase_order/{$order->PONumber}/{$fileName}";
        $directory = dirname($path);

        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory, 0775, true);
        }

        Storage::put($path, $pdf->output());

        PurchaseOrderPdf::create([
            'purchase_order_id' => $order->ID,
            'file_path' => $path,
            'file_name' => basename($path),
        ]);

        return $path;
    }

    public function storeInvoices(PurchaseOrder $order, array $files): void
    {
        $directory = $this->invoiceDirectory($order);

        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory, 0775, true);
        }

        foreach ($files as $file) {
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safe = trim(preg_replace("/[^A-Za-z0-9_-]+/", "_", $name), "_") ?: "invoice";
            $file->storeAs($directory, "{$safe}_" . time() . "_" . bin2hex(random_bytes(2)) . ".pdf");
        }
    }

    public function invoicePaths(PurchaseOrder $order): array
    {
        return Storage::files($this->invoiceDirectory($order));
    }

    private function invoiceDirectory(PurchaseOrder $order): string
    {
        return "pdfs/purchase_order/{$order->PONumber}/invoices";
    }

    /**
     * The single perspective the stored/printed document is rendered from,
     * derived from the PO operation (HQ types follow their standard ones).
     */
    private function canonicalPerspective(PurchaseOrder $order): string
    {
        $type = PurchaseOrderTypeEnum::tryFrom((int) $order->POType);

        return match ($type?->operation((int) $order->SupplierID, (int) $order->OtherStoreID)) {
            'TransferIN'       => 'to_store',
            'TransferOut'      => 'from_store',
            'PO'               => 'supplier',
            'ReturnToSupplier' => 'return_to_supplier',
            default            => 'default',
        };
    }

    private function filePrefix(string $perspective): string
    {
        return match ($perspective) {
            'supplier'           => 'purchase_order',
            'return_to_supplier' => 'return_to_supplier',
            default              => 'transfer_request',
        };
    }
}
