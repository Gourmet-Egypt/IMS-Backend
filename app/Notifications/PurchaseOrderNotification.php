<?php

namespace App\Notifications;

use App\Models\PurchaseOrderEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $purchaseOrder;
    public $pdfs;
    public $perspective;

    /**
     * Create a new notification instance.
     */
    public function __construct($purchaseOrder, $pdfs, $perspective = 'default')
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->pdfs = $pdfs;
        $this->perspective = $perspective;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $fromStoreName = $this->getFromStoreName();
        $subject = $this->getEmailSubject();

        return new Envelope(
            from: new Address(config('mail.from.address'), $fromStoreName),
            subject: $subject,
        );
    }

    /**
     * Get the email sender store name based on recipient
     */
    protected function getFromStoreName()
    {
        if ((int)$this->purchaseOrder->POType == 2) {
            return $this->perspective === 'to_store'
                ? $this->purchaseOrder->currentStore->Name ?? 'Unknown Store'
                : $this->purchaseOrder->otherStore->Name ?? 'Unknown Store';
        }

        if ((int)$this->purchaseOrder->POType == 3) {
            return $this->perspective === 'from_store'
                ? $this->purchaseOrder->currentStore->Name ?? 'Unknown Store'
                : $this->purchaseOrder->otherStore->Name ?? 'Unknown Store';
        }

        return $this->purchaseOrder->currentStore->Name ?? 'Unknown Store';
    }

    /**
     * Build the email subject based on purchase order type and perspective
     */
    protected function getEmailSubject()
    {
        [$fromStore, $toStore] = $this->getStoreNamesForFlow();
        $title = $this->purchaseOrder->title;
        $id = $this->purchaseOrder->ID;

        if ($this->perspective === 'from_store') {
            return "Transfer OUT from {$fromStore} to {$toStore} - #{$id} - {$title}";
        }

        if ($this->perspective === 'to_store') {
            return "Transfer IN from {$fromStore} to {$toStore} - #{$id} - {$title}";
        }

        return "Purchase Order #{$id} - {$title}";
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        [$fromStore, $toStore] = $this->getStoreNamesForFlow();

        return new Content(
            view: 'emails.purchase_order',
            with: [
                'purchaseOrder' => $this->purchaseOrder,
                'fromStore' => $fromStore,
                'toStore' => $toStore,
                'perspective' => $this->perspective,
            ],
        );
    }

    /**
     * Get the from/to store names based on goods flow direction
     */
    protected function getStoreNamesForFlow()
    {
        $currentStoreName = $this->purchaseOrder->currentStore->Name ?? 'Unknown';
        $otherStoreName = $this->purchaseOrder->otherStore->Name ?? 'Unknown';

        if ((int)$this->purchaseOrder->POType == 2) {
            return [$otherStoreName, $currentStoreName];
        }

        return [$currentStoreName, $otherStoreName];
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Generate PDF on-demand based on perspective
        $pdf = $this->generatePdfForPerspective();

        if ($pdf) {
            $fileName = "transfer_request_{$this->purchaseOrder->PONumber}_{$this->perspective}.pdf";
            $attachments[] = Attachment::fromData(fn () => $pdf->output(), $fileName)
                ->withMime('application/pdf');
        }

        return $attachments;
    }

    /**
     * Generate PDF based on recipient perspective
     */
    protected function generatePdfForPerspective()
    {
        // Load necessary relationships including transfer request
        $this->purchaseOrder->load([
            'condition',
            'entries.infos',
            'entries.item',
            'entries.transferRequest' => function($query) {
                $query->with('items');
            },
            'currentStore',
            'otherStore'
        ]);

        \Illuminate\Support\Facades\Log::info("Generating PDF with perspective: {$this->perspective} for PO #{$this->purchaseOrder->ID}");

        // Transform items data - one row per item with summed quantities
        $items = $this->purchaseOrder->entries->map(function ($entry) {
            $infos = $entry->infos;

            // Get quantity from transfer request item
            $quantityRequested = $entry->QuantityOrdered ?? 0; // Default

            \Illuminate\Support\Facades\Log::info("Processing entry", [
                'entry_id' => $entry->ID,
                'item_id' => $entry->ItemID,
                'has_transfer_request' => $entry->transferRequest ? 'yes' : 'no',
                'transfer_request_id' => $entry->transferRequest?->id,
                'items_count' => $entry->transferRequest?->items?->count(),
            ]);

            if ($entry->transferRequest && $entry->transferRequest->items) {
                // Match by Item.ID since:
                // - transfer_request_item.item_id -> Item.HQID
                // - PurchaseOrderEntry.ItemID -> Item.ID
                // Both refer to the same item but use different keys

                $matchingItem = $entry->transferRequest->items->first(function($item) use ($entry) {
                    // item.ID (from transfer request via HQID) should equal entry.ItemID (Item.ID)
                    return $item->ID === $entry->ItemID;
                });

                if ($matchingItem && isset($matchingItem->pivot->quantity)) {
                    $quantityRequested = $matchingItem->pivot->quantity;
                }
            }

            // Sum quantity_issued for all infos of the same item
            $totalQuantityIssued = $infos->sum('quantity_issued');
            $totalQuantityReceived = $entry->QuantityReceivedToDate ?? 0;

            // Set values based on perspective AND POType
            $poType = (int)$this->purchaseOrder->POType;

            if ($poType == 3) {
                // TransferOUT: Goods are being sent out
                if ($this->perspective === 'from_store') {
                    // FROM store (sending): show what was issued
                    $displayQuantityIssued = $totalQuantityIssued;
                    $displayQuantityReceived = 0;
                } else {
                    // TO store (receiving): nothing received yet at commit time
                    $displayQuantityIssued = 0;
                    $displayQuantityReceived = 0;
                }
            } elseif ($poType == 2) {
                // TransferIN: Goods are being received
                if ($this->perspective === 'to_store') {
                    // TO store (receiving): show what was received
                    $displayQuantityIssued = 0;
                    $displayQuantityReceived = $totalQuantityReceived;
                } else {
                    // FROM store (sending from other store): nothing from their perspective
                    $displayQuantityIssued = 0;
                    $displayQuantityReceived = 0;
                }
            } else {
                // Default: show both actual values
                $displayQuantityIssued = $totalQuantityIssued;
                $displayQuantityReceived = $totalQuantityReceived;
            }

            $itemData = (object)[
                'lookupcode' => $entry->item->ItemLookupCode ?? 'N/A',
                'description' => $entry->ItemDescription ?? 'N/A',
                'quantity_requested' => $quantityRequested,
                'quantity_received' => $displayQuantityReceived,
                'quantity_issued' => $displayQuantityIssued,
                'production_date' => $infos->first()?->production_date ?? null,
                'expire_date' => $infos->first()?->expire_date ?? null,
            ];

            return $itemData;
        });

        $data = [
            'purchaseOrder' => $this->purchaseOrder,
            'items' => $items,
            'condition' => $this->purchaseOrder->condition,
            'perspective' => $this->perspective
        ];

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.purchase_order', $data);
    }
}
