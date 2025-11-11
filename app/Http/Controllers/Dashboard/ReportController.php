<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\Reports\TransferListResource;
use App\Models\PurchaseOrder;
use App\Traits\Responses;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    use Responses ;
    public function PO_quantity_max_thousand(PurchaseOrder $purchaseOrder)
    {
        $items = PurchaseOrder::quantityMaxThousand($purchaseOrder);
    }

    public function TransferList(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder = $purchaseOrder->load([
            'entries',
            'entries.infos',
            'entries.HQItem',
            'entries.HQItem.department',
            'entries.HQItem.category'
        ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Transfer In List Report',
            data: new TransferListResource($purchaseOrder),
        );
    }
}
