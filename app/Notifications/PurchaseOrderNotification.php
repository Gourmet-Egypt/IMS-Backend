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

    /**
     * Create a new notification instance.
     */
    public function __construct($purchaseOrder, $pdfs)
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->pdfs = $pdfs;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $fromStore = $this->purchaseOrder->currentStore->Name ?? 'Unknown Store';
        $subject = $this->getEmailSubject();

        return new Envelope(
            from: new Address(config('mail.from.address'), $fromStore),
            subject: $subject,
        );
    }

    /**
     * Build the email subject based on purchase order type
     */
    protected function getEmailSubject()
    {
        $fromStore = $this->purchaseOrder->currentStore->Name ?? 'Unknown Store';
        $toStore = $this->purchaseOrder->otherStore->Name ?? 'Unknown Store';
        $title = $this->purchaseOrder->title;
        $id = $this->purchaseOrder->ID;

        if ($this->purchaseOrder->type == 2) {
            return "Transfer IN from {$fromStore} - #{$id} - {$title}";
        }

        if ($this->purchaseOrder->type == 3) {
            return "Transfer OUT to {$toStore} - #{$id} - {$title}";
        }

        return "Purchase Order #{$id} - {$title}";
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.purchase_order',
            with: [
                'purchaseOrder' => $this->purchaseOrder,
                'fromStore' => $this->purchaseOrder->currentStore->Name ?? 'Unknown',
                'toStore' => $this->purchaseOrder->otherStore->Name ?? 'Unknown',
            ],
        );
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
