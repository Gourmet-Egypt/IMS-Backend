<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PurchaseOrderTestMail extends Mailable
{


    /**
     * Create a new message instance.
     */
    public $purchaseOrder;

    public function __construct($purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    public function build()
    {
        return $this->subject('Test Purchase Order Email')
            ->view('Emails.purchase_order_test');
    }
}
