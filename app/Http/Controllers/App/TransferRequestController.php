<?php

namespace App\Http\Controllers\App;

use App\Enums\TransferRequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\TransferRequest\StoreTransferRequest;
use App\Http\Requests\App\TransferRequest\UpdateTransferRequest;
use App\Http\Resources\App\TransferRequest\TransferRequestResource;
use App\Models\TransferRequest;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class TransferRequestController extends Controller
{
    use Responses;

    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $transferRequests = TransferRequest::with('items')->where('status', 'open')->paginate(15);

        return $this->appSuccessPaginated(
            status: Response::HTTP_OK,
            message: 'Transfer Requests Retrieved Successfully',
            data: TransferRequestResource::collection($transferRequests),
        );
    }

    public function store(StoreTransferRequest $request)
    {
        $type = $request->input('type');
        $userStoreId = $request->user()->store_id;


        $transferRequest = TransferRequest::create([
            'title' => $request->input('title'),
            'type' => $type,
            'store_id' => $userStoreId,
            'other_store_id' => $request->input('other_store_id'),
            'status' => TransferRequestStatusEnum::OPEN->value,
            'delivery_date' => $request->input('delivery_date'),
            'created_at' => now(),
            'updated_at' => null,
        ]);


        return $this->success(
            status: Response::HTTP_CREATED,
            message: 'TransferRequest Created successfully',
            data: new TransferRequestResource($transferRequest)
        );
    }


    public function show(TransferRequest $transferRequest)
    {

        return $this->success(
            status: Response::HTTP_OK,
            message: 'TransferRequest retrieved successfully',
            data: new TransferRequestResource($transferRequest->load('items'))
        );
    }

    public function destroy(TransferRequest $transferRequest)
    {
        $transferRequest->delete();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'TransferRequest deleted successfully',
            data: new TransferRequestResource($transferRequest)
        );
    }

    public function createOrder(Request $request, TransferRequest $transferRequest)
    {
        if (!$transferRequest->items()->exists()) {
            return $this->error(
                status: Response::HTTP_NOT_ACCEPTABLE,
                message: 'No items were found',
                data: []
            );
        }

        $cashier = $request->user()->cashier;

        $data = [
            "Order" => [
                "POTitle" => $transferRequest->title,
                "transactionType" => $transferRequest->type,
                "StoreID" => (int) $transferRequest->store_id,
                "OtherStoreID" => (int) $transferRequest->other_store_id,
                "SupplierID" => 0,
                "HH_ID" => (string) $transferRequest->id,
                "CashierID" => $cashier->ID,
            ],
            "OrderItems" => $transferRequest->items->map(function ($item) {
                return [
                    "ItemLookupcode" => (string) $item->ItemLookupCode,
                    "QTY" => (float) $item->pivot->quantity,
                ];
            })->values()->toArray(),
        ];

        $response = Http::withoutVerifying()
            ->asJson()
            ->post('http://192.168.23.19/api/create-order', $data);


        if ($response->failed()) {
            return $this->error(
                status: $response->status() ?? Response::HTTP_INTERNAL_SERVER_ERROR,
                message: $response->json('message') ?? 'Failed to update transfer request'
            );
        }

        $body = $response->json();

        $transferRequest->update([
            'status' => TransferRequestStatusEnum::CLOSED,
            'purchase_order_id' => $body['id'] ?? null,
        ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Transfer request status updated successfully',
            data: new TransferRequestResource($transferRequest)
        );

    }

    public function update(UpdateTransferRequest $request, TransferRequest $transferRequest)
    {

        $transferRequest->update($request->validated());

        return $this->success(
            status: Response::HTTP_OK,
            message: 'TransferRequest updated successfully',
            data: new TransferRequestResource($transferRequest->fresh())
        );
    }


}
