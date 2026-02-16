<?php

namespace App\Http\Controllers\App;

use App\Enums\PurchaseOrderTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\PurchaseOrder\CommitOrderRequest;
use App\Http\Requests\App\PurchaseOrderEntry\UpdatePurchaseOrderEntryInfosRequest;
use App\Http\Resources\App\Offline\PurchaseOrderEntryResource;
use App\Http\Resources\App\Offline\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderEntry;
use App\Models\PurchaseOrderProcessStart;
use App\Services\CommitOrderService;
use App\Traits\Responses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PurchaseOrderController extends Controller
{
    use Responses;

    public function index(): \Illuminate\Http\JsonResponse
    {
        $purchaseOrders = PurchaseOrder::storeFilter()->get();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Purchase Orders Retrieved Successfully',
            data: PurchaseOrderResource::collection($purchaseOrders)
        );
    }

    public function show(PurchaseOrder $purchaseOrder): \Illuminate\Http\JsonResponse
    {
        $purchaseOrder = $purchaseOrder->load(['condition', 'entries', 'entries.infos']);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'PurchaseOrder retrieved successfully',
            data: new  \App\Http\Resources\App\Offline\PurchaseOrderResource($purchaseOrder),
        );
    }


    public function offline(): \Illuminate\Http\JsonResponse
    {
        $purchaseOrders = PurchaseOrder::Type()->with(['condition', 'entries', 'entries.infos'])->where('status',
            0)->paginate(15);

        return $this->AppSuccessPaginated(
            status: Response::HTTP_OK,
            message: 'Purchase Orders Retrieved Successfully',
            data: PurchaseOrderResource::collection($purchaseOrders),
        );
    }


    public function startProcess(PurchaseOrder $purchaseOrder): \Illuminate\Http\JsonResponse
    {
        $existingStart = PurchaseOrderProcessStart::where('purchase_order_id', $purchaseOrder->ID)->first();

        if ($existingStart) {
            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Process already started'
            );
        }

        $processStart = PurchaseOrderProcessStart::create([
            'purchase_order_id' => $purchaseOrder->ID,
            'start_date' => now(),
        ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Process started successfully',
            data: [
                'purchase_order_id' => $processStart->purchase_order_id,
                'start_date' => $processStart->start_date,
            ]
        );
    }

    public function commitOrder(
        PurchaseOrder $purchaseOrder,
        CommitOrderRequest $request,
        CommitOrderService $service
    ): \Illuminate\Http\JsonResponse {
        return $service->commit($purchaseOrder, $request);
    }


    public function allInfos(PurchaseOrderEntry $purchaseOrderEntry): \Illuminate\Http\JsonResponse
    {
        $purchaseOrderEntry = $purchaseOrderEntry->load(['infos']);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Infos retrieved successfully',
            data: new PurchaseOrderEntryResource($purchaseOrderEntry),
        );
    }


    public function updateInfos(
        UpdatePurchaseOrderEntryInfosRequest $request,
        PurchaseOrderEntry $purchaseOrderEntry
    ): \Illuminate\Http\JsonResponse {
        $validated = $request->validated();

        $server = config('database.connections.sqlsrv.host');
        $storeId = DB::table('Configuration')->select('StoreID')->value('StoreID');

        $data = [
            "StoreID" => $storeId,
            "transactionType" => PurchaseOrderTypeEnum::tryFrom($purchaseOrderEntry->purchaseOrder->POType)?->name,
            "purchase_order_id" => $purchaseOrderEntry->PurchaseOrderID,
            "purchase_order_entry_id" => $purchaseOrderEntry->ID,
            "Batches" => $validated['Batches'],
        ];

        $response = Http::withoutVerifying()
            ->asJson()
            ->post("http://$server/api/update-order-details", $data);

        if ($response->status() === 200) {

            return $this->success(
                status: Response::HTTP_OK,
                message: 'Purchase order entry updated successfully',
                data: new PurchaseOrderEntryResource($purchaseOrderEntry->load(['infos'])),
            );
        } else {
            return $this->error(
                status: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: $response
            );
        }
    }


    public function test(PurchaseOrder $purchaseOrder)
    {

        $purchaseOrder = $purchaseOrder->load(['condition', 'entries', 'entries.infos']);

        PurchaseOrderCommitted::dispatch($purchaseOrder);
    }
}
