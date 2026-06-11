<?php

namespace App\Http\Controllers\App;

use App\Enums\TransferRequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\TransferRequest\StoreTransferRequest;
use App\Http\Requests\App\TransferRequest\UpdateTransferRequest;
use App\Http\Resources\App\TransferRequest\TransferRequestResource;
use App\Models\Store;
use App\Models\TransferRequest;
use App\Services\CreateOrderService;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $user = $request->user();
        $type = $request->input('type');
        $otherStoreId = $request->input('other_store_id');

        $stores = Store::whereIn('ID', [$user->store_id, $otherStoreId])
            ->pluck('Name', 'ID');

        $userStoreName = $stores[$user->store_id];
        $otherStoreName = $stores[$otherStoreId];

        // Generate title with proper logic
        $title = $request->input('title') ?? $this->generateTransferTitle(
            $type,
            $userStoreName,
            $otherStoreName
        );

        // Create transfer request
        $transferRequest = TransferRequest::create([
            'title' => $title,
            'type' => $type,
            'store_id' => $user->store_id,
            'other_store_id' => $otherStoreId,
            'status' => TransferRequestStatusEnum::OPEN->value,
            'delivery_date' => $request->input('delivery_date'),
        ]);

        return $this->success(
            status: Response::HTTP_CREATED,
            message: 'Transfer request created successfully',
            data: new TransferRequestResource($transferRequest)
        );
    }

    /**
     * Generate a default title for the transfer request
     */
    private function generateTransferTitle(
        string $type,
        string $fromStore,
        string $toStore
    ): string {
        return $type === 'TransferIN'
            ? "Request from {$fromStore} to {$toStore}"
            : "Transfer Out from {$fromStore} to {$toStore}";
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

    public function createOrder(
        TransferRequest $transferRequest,
        Request $request,
        CreateOrderService $service
    ) {
        return $service->create($transferRequest, $request);
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

