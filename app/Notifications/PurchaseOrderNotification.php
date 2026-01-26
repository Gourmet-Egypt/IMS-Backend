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
        if ($this->purchaseOrder->POType == 2) {
            return $this->perspective === 'to_store'
                ? $this->purchaseOrder->currentStore->Name ?? 'Unknown Store'
                : $this->purchaseOrder->otherStore->Name ?? 'Unknown Store';
        }

        if ($this->purchaseOrder->POType == 3) {
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

        if ($this->purchaseOrder->POType == 2) {
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

        foreach ($this->pdfs as $pdf) {
            if (Storage::exists($pdf->file_path)) {
                $attachments[] = Attachment::fromStorage($pdf->file_path)
                    ->as($pdf->file_name)
                    ->withMime('application/pdf');
            }
        }

        return $attachments;
    }
}
