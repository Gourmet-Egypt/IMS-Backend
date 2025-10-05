<?php

namespace App\Http\Controllers;

use App\Enums\TransferRequestStatusEnum;
use App\Enums\TransferRequestTypeEnum;
use App\Http\Requests\TransferRequest\StoreTransferRequest;
use App\Http\Requests\TransferRequest\UpdateTransferRequest;
use App\Http\Resources\TransferRequest\ShowTransferRequestResource;
use App\Http\Resources\TransferRequest\TransferRequestResource;
use App\Models\PurchaseOrder;
use App\Models\TransferRequest;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TransferRequestController extends Controller
{
    use Responses;

    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $transferRequests = TransferRequest::paginate(15);

        return $this->successPaginated(
            status: Response::HTTP_OK,
            message: 'Transfer Requests Retrieved Successfully',
            data: TransferRequestResource::collection($transferRequests)
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
            data: new ShowTransferRequestResource($transferRequest->load('items'))
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
            data: null
        );
    }

    public function changeStatus(TransferRequest $transferRequest)
    {
        $transferRequest->update(['status' => TransferRequestStatusEnum::CLOSED]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'TransferRequest status updated successfully',
            data: new TransferRequestResource($transferRequest)
        );
    }

    public function TransferOutList()
    {
        $store_id = auth()->user()->store_id ;
        $requests = PurchaseOrder::store($store_id) ;

        dd($requests);
    }
}
