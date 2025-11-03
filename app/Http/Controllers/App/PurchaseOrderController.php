<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\App\PurchaseOrder\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Traits\Responses;
use Illuminate\Http\Response;

class PurchaseOrderController extends Controller
{
    use Responses ;

    public function index()
    {
        $purchaseOrders = PurchaseOrder::store()->get();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Purchase Orders Retrieved Successfully',
            data: PurchaseOrderResource::collection($purchaseOrders)
        );
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder = $purchaseOrder->load(['condition' , 'entries' , 'entries.infos' ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'PurchaseOrder retrieved successfully',
            data:new  \App\Http\Resources\App\Offline\PurchaseOrderResource($purchaseOrder) ,
        );
    }


    public function offline()
    {
        $purchaseOrders = PurchaseOrder::with(['condition' , 'entries' , 'entries.infos' ])->where('status' , 0)->paginate(15);

        return $this->AppSuccessPaginated(
            status: Response::HTTP_OK,
            message: 'Purchase Orders Retrieved Successfully',
            data: \App\Http\Resources\App\Offline\PurchaseOrderResource::collection($purchaseOrders) ,
        );
    }
}
