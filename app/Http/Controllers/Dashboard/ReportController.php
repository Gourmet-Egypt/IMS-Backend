<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\Reports\EntryDetailsResource;
use App\Http\Resources\Dashboard\Reports\TransferListResource;
use App\Http\Resources\Dashboard\Reports\TransferStatusResource;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderEntry;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    use Responses;

    public function transferList($id)
    {
        $purchaseOrder = PurchaseOrder::on('sqlsrv_rms')->transferList($id)->first();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Transfer In List Report',
            data: new TransferListResource($purchaseOrder),
        );
    }


    public function transferStatus(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::on('sqlsrv_rms')->transferStatus($id)->first();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Transfer Status Report',
            data: new TransferStatusResource($purchaseOrder),
        );
    }

    public function store()
    {
        $data = PurchaseOrder::store();
        return $this->success(
            status: Response::HTTP_OK,
            message: 'Store Date Successfully',
            data: $data
        );
    }

    public function allStores()
    {
        $data = PurchaseOrder::allStores();

        return $data;
    }

    public function entryDetails($id)
    {
        $entry = PurchaseOrderEntry::on('sqlsrv_rms')
            ->entryDetails($id)
            ->first();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Entry Details Report',
            data: new EntryDetailsResource($entry),
        );
    }


}
