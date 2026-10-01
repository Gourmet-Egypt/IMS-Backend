<?php

namespace App\Services\Commit;

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
        $pdf = $this->builder->build($order, $this->canonicalPerspective($order));

        $fileName  = "transfer_request_{$order->PONumber}_" . time() . ".pdf";
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

    /**
     * The single perspective the stored/printed document is rendered from,
     * derived from the PO type (HQ 4/5 follow 2/3). Supplier POs use 'default'.
     */
    private function canonicalPerspective(PurchaseOrder $order): string
    {
        return match ((string) $order->POType) {
            '2', '4' => 'to_store',
            '3', '5' => 'from_store',
            default  => 'default',
        };
    }
}
