<?php

namespace App\Http\Controllers\App;

use App\Enums\TransferRequestStatusEnum;
use App\Enums\TransferRequestTypeEnum;
use App\Events\TransferRequestCommitted;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\TransferRequest\StoreTransferRequest;
use App\Http\Requests\App\TransferRequest\UpdateTransferRequest;
use App\Http\Resources\App\Offline\PurchaseOrderResource;
use App\Http\Resources\App\TransferRequest\TransferRequestResource;
use App\Models\PurchaseOrder;
use App\Models\TransferRequest;
use App\Traits\Responses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransferRequestController extends Controller
{
    use Responses;

    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $transferRequests = TransferRequest::with('items')->where('status' , 'open')->paginate(15);

        return $this->appSuccessPaginated(
            status: Response::HTTP_OK,
            message: 'Transfer Requests Retrieved Successfully',
            data: TransferRequestResource::collection($transferRequests) ,
        );
    }

    public function store(StoreTransferRequest $request)
    {
        $transferRequest = TransferRequest::create([
            'title' => $request->input('title'),
            'from_store_id' => Auth::user()->store_id,
            'to_store_id' => $request->input('to_store_id'),
            'status' => TransferRequestStatusEnum::OPEN,
            'type' => TransferRequestTypeEnum::IN,
            'delivery_date' => $request->input('delivery_date'),
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

    public function update(UpdateTransferRequest $request, TransferRequest $transferRequest)
    {
        $transferRequest->update($request->validated());

        return $this->success(
            status: Response::HTTP_OK,
            message: 'TransferRequest updated successfully',
            data: new TransferRequestResource($transferRequest->fresh())
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

    public function createOrder(TransferRequest $transferRequest)
    {
        if (!$transferRequest->items()->exists()) {
            return $this->error(
                status: Response::HTTP_NOT_ACCEPTABLE,
                message: 'No items were found' ,
                data: []
            );
        }

        $data = [
            "Order" => [
                "transactionType" => "TransferIN",
                "StoreID" => (int) $transferRequest->from_store_id,
                "OtherStoreID" => (int) $transferRequest->to_store_id,
                "SupplierID" => 0,
                "HH_ID" => (string) $transferRequest->id,
                "CashierID" => 73,
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


        if ($response->status() === 200) {
            $body = $response->json();

            $transferRequest->update([
                'status' => TransferRequestStatusEnum::CLOSED,
                'purchase_order_id' => $body['id'] ?? null,
            ]);
        } else {

            return $this->error(
                status: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: $response
            );
        }

        return $this->success(
            status: Response::HTTP_OK,
            message: 'TransferRequest status updated successfully',
            data: new TransferRequestResource($transferRequest)
        );
    }

    public function commitOrder()
    {
        // take the data from mahmoud

        // call the endpoint from tony
        $transferRequest = [
            'transfer_request' => [] ,
            'conditions' => [] ,
            'infos' => [] ,
        ];
        // check if the order is committed and the response is 200
        $response = Http::withoutVerifying()
            ->asJson()
            ->post('http://192.168.23.19/api/create-order', $transferRequest);


        if ($response->status() === 200) {
            // fire the event
            event(new TransferRequestCommitted($transferRequest));
        }
        // one event , two listeners , two services

        // first listener for pdf -> pdf service -> create the three pdfs -> will return to mahmoud

        // second listener for mails -> mail service -> create the three mails ( determine the type of the order to choose the temp and the users the temp will send to )



    }

}
