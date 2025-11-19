<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderCommitted
{
    use Dispatchable, SerializesModels;

    public $purchaseOrder;

    /**
     * Create a new event instance.
     */
    public function __construct($purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }
}
