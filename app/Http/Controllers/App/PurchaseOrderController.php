<?php

namespace App\Http\Controllers\App;

use App\Events\PurchaseOrderCommitted;
use App\Http\Controllers\Controller;
use App\Http\Resources\App\Offline\PurchaseOrderEntryResource;
use App\Http\Resources\App\PurchaseOrder\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderEntry;
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
        $purchaseOrders = PurchaseOrder::Type()->with(['condition' , 'entries' , 'entries.infos' ])->where('status' , 0)->paginate(15);

        return $this->AppSuccessPaginated(
            status: Response::HTTP_OK,
            message: 'Purchase Orders Retrieved Successfully',
            data: \App\Http\Resources\App\Offline\PurchaseOrderResource::collection($purchaseOrders) ,
        );
    }

//    public function test(PurchaseOrder $purchaseOrder)
//    {
//
//        $purchaseOrder = $purchaseOrder->load(['condition' , 'entries' , 'entries.infos' ]);
//
//        PurchaseOrderCommitted::dispatch($purchaseOrder);
//    }


    public function allInfos(PurchaseOrderEntry $purchaseOrderEntry)
    {
        $purchaseOrderEntry = $purchaseOrderEntry->load(['infos']);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Infos retrieved successfully',
            data: new PurchaseOrderEntryResource($purchaseOrderEntry) ,
        );
    }
}
