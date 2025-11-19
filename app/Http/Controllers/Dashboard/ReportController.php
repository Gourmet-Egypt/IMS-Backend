<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\Reports\TransferListResource;
use App\Http\Resources\Dashboard\Reports\TransferStatusResource;
use App\Models\PurchaseOrder;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    use Responses;

    public function TransferList($id)
    {
        $purchaseOrder = PurchaseOrder::on('sqlsrv_rms')->transferReports($id);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Transfer In List Report',
            data: new TransferListResource($purchaseOrder),
        );
    }


    public function TransferStatus(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::on('sqlsrv_rms')->transferReports($id);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Transfer Status Report',
            data: new TransferStatusResource($purchaseOrder),
        );
    }

}
