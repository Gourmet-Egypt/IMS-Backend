<?php

namespace App\Http\Controllers\App;

use App\Enums\PurchaseOrderTypeEnum;
use App\Events\PurchaseOrderCommitted;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\PurchaseOrder\CommitOrderRequest;
use App\Http\Requests\App\PurchaseOrderEntry\UpdatePurchaseOrderEntryInfosRequest;
use App\Http\Resources\App\Offline\PurchaseOrderEntryResource;
use App\Http\Resources\App\Offline\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderEntry;
use App\Traits\Responses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class PurchaseOrderController extends Controller
{
    use Responses;

    public function index(): \Illuminate\Http\JsonResponse
    {
        $purchaseOrders = PurchaseOrder::store()->get();

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


    public function commitOrder(
        PurchaseOrder $purchaseOrder,
        CommitOrderRequest $request
    ): \Illuminate\Http\JsonResponse {
        $cashier = auth()->user()->cashier;

        if (!$cashier) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Cashier not found'
            );
        }

        $poTypeEnum = PurchaseOrderTypeEnum::fromValue((int) $purchaseOrder->POType);

        if (!$poTypeEnum) {
            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Invalid purchase order type'
            );
        }

        $baseData = [
            "ID" => $purchaseOrder->ID,
            "transactionType" => $poTypeEnum->label(),
            "StoreID" => (int) $purchaseOrder->StoreID,
            "CashierID" => (int) $cashier->ID
        ];

        $orderSpecific = match ($purchaseOrder->POType) {
            '3' => [
                "VehicleType" => (string) $request->input('VehicleTypeID'),
                "Vehicle_tempOut" => $request->input('Vehicle_tempOut'),
                "DeliveryPermitNumber" => $request->input('DeliveryPermitNumber'),
                "Notes" => $request->input('Notes', ''),
                "seal_number" => $request->input('seal_number'),
            ],


            '2' => [
                "Vehicle_tempIN" => $request->input('Vehicle_tempIN'),
            ],

            default => [],
        };


        $data = [
            "Order" => array_merge($baseData, $orderSpecific),
        ];


        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->asJson()
                ->post('http://192.168.23.19/api/commit-order', $data);

            if (!$response->successful()) {
                $responseData = $response->json();

                $errorMessage = 'Failed to commit order';

                if (isset($responseData['message'])) {
                    if (is_string($responseData['message'])) {
                        preg_match('/"message":\s*"([^"]+)"/', $responseData['message'], $matches);
                        if (!empty($matches[1])) {
                            $errorMessage = $matches[1];
                        } else {
                            $errorMessage = $responseData['message'];
                        }
                    } elseif (is_array($responseData['message'])) {
                        $errorMessage = json_encode($responseData['message']);
                    } else {
                        $errorMessage = $responseData['message'];
                    }
                }

                if (strpos($errorMessage, ':') !== false) {
                    $errorMessage = trim(substr($errorMessage, strpos($errorMessage, ':') + 1));
                }

                return $this->error(
                    status: Response::HTTP_INTERNAL_SERVER_ERROR,
                    message: $errorMessage
                );
            }

            PurchaseOrderCommitted::dispatch($purchaseOrder);

            $purchaseOrder->load(['condition', 'entries', 'entries.infos']);

            $endpointResponse = $response->json();


            return $this->success(
                status: Response::HTTP_OK,
                message: 'Purchase Order Committed Successfully',
                data: new PurchaseOrderResource($purchaseOrder),
            );

        } catch (\Exception $e) {
            return $this->error(
                status: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: 'Failed to commit order: '.$e->getMessage()
            );
        }
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
        $data = [
            "StoreID" => $purchaseOrderEntry->StoreID,
            "transactionType" => PurchaseOrderTypeEnum::fromValue($purchaseOrderEntry->purchaseOrder->POType)?->label(),
            "purchase_order_id" => $purchaseOrderEntry->PurchaseOrderID,
            "purchase_order_entry_id" => $purchaseOrderEntry->ID,
            "Batches" => $request->post('Batches'),
        ];

        $response = Http::withoutVerifying()
            ->asJson()
            ->post('http://192.168.23.19/api/update-order-details', $data);

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
