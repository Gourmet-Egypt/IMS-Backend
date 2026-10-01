<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Thin notification for the rebuilt commit flow. Every per-type decision
 * (subject, sender, store flow, and the PDF) is precomputed by CommitEmailService,
 * so this mailable only renders. The legacy PurchaseOrderNotification is untouched.
 */
class CommitPurchaseOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $purchaseOrder,
        public $pdf,
        public $subject,
        public string $senderName,
        public string $fromStore,
        public string $toStore,
        public string $perspective,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), $this->senderName),
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        $this->purchaseOrder->loadMissing('condition');

        return new Content(
            view: $this->resolveView(),
            with: [
                'purchaseOrder' => $this->purchaseOrder,
                'fromStore' => $this->fromStore,
                'toStore' => $this->toStore,
            ],
        );
    }

    /**
     * One blade per case — no perspective conditionals inside the templates.
     * NOTE: must not be named `view()` — that name belongs to Mailable's own
     * fluent view-setter, which the framework calls internally; overriding it
     * breaks content hydration (and a zero-arg call crashes outright since the
     * framework's `view($view, $data)` has no default for $view).
     */
    private function resolveView(): string
    {
        return match ($this->perspective) {
            'from_store' => 'commit.emails.transfer_out',
            'to_store' => 'commit.emails.transfer_in',
            default => 'commit.emails.default',
        };
    }

    public function attachments(): array
    {
        if (!$this->pdf) {
            return [];
        }

        $label = $this->perspective === 'from_store' ? 'out' : 'in';
        $fileName = "transfer_{$label}_{$this->purchaseOrder->PONumber}.pdf";

        return [
            Attachment::fromData(fn() => $this->pdf->output(), $fileName)
                ->withMime('application/pdf'),
        ];
    }
}
