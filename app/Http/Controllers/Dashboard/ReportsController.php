<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;

class ReportsController extends Controller
{
    public function PO_quantity_max_thousand(PurchaseOrder $purchaseOrder)
    {
        $items = PurchaseOrder::quantityMaxThousand($purchaseOrder);
    }
}
