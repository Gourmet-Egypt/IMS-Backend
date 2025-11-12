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

    public function TransferList($id)
    {
        $purchaseOrder = PurchaseOrder::on('sqlsrv_rms')->with([
            'entries',
            'entries.infos',
//            'entries.HQItem',
//            'entries.HQItem.department',
//            'entries.HQItem.category'
        ])->findOrFail($id);

        dd($purchaseOrder);


        return $this->success(
            status: Response::HTTP_OK,
            message: 'Transfer In List Report',
            data: new TransferListResource($purchaseOrder),
        );
    }
}
