<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequestItem\StoreTransferRequestItemRequest;
use App\Http\Requests\TransferRequestItem\UpdateTransferRequestItemRequest;
use App\Http\Resources\TransferRequest\ShowTransferRequestResource;
use App\Http\Resources\TransferRequestItemResource;
use App\Models\TransferRequest;
use App\Models\Item;
use App\Models\TransferRequestItem;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransferRequestItemController extends Controller
{
    use Responses;

    public function store(StoreTransferRequestItemRequest $request)
    {
        $transferRequest = TransferRequest::findOrFail($request->input('transfer_request_id'));
        $items = $request->input('items', []);

        $attachedItems = [];

        foreach ($items as $itemData) {

            $exists = $transferRequest->items()->where('item_id', $itemData['id'])->exists();

            if ($exists) {
                return $this->error(
                    status: Response::HTTP_CONFLICT,
                    message: "Item with ID {$itemData['id']} is already attached to this transfer request."
                );
            }


            $transferRequest->items()->attach($itemData['id'], [
                'quantity' => $itemData['quantity'],
                'notes' => $itemData['notes'] ?? null
            ]);

            $attachedItem = $transferRequest->items()
                ->where('item_id', $itemData['id'])
                ->first();

            $attachedItems[] = $attachedItem;
        }

        return $this->success(
            status: Response::HTTP_CREATED,
            message: 'Items added successfully',
            data: TransferRequestItemResource::collection($attachedItems)
        );
    }


    public function update(UpdateTransferRequestItemRequest $request, TransferRequest $transferRequest)
    {

        $itemPivot = TransferRequestItem::where('item_id', $request->item_id)
            ->where('transfer_request_id', $transferRequest->id)
            ->first();

        if (!$itemPivot) {
            return $this->error(
                status: Response::HTTP_UNPROCESSABLE_ENTITY,
                message: 'Item not found in this transfer request'
            );
        }


        $updateData = [];
        if ($request->filled('quantity')) {
            $updateData['quantity'] = $request->quantity;
        }
        if ($request->filled('notes')) {
            $updateData['notes'] = $request->notes;
        }

        if (empty($updateData)) {
            return $this->error(
                status: Response::HTTP_UNPROCESSABLE_ENTITY,
                message: 'No data provided for update'
            );
        }

        $transferRequest->items()->updateExistingPivot($request->item_id, $updateData);


        $updatedItem = $transferRequest->items()
            ->where('item_id', $request->item_id)
            ->first();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Item updated successfully',
            data: new TransferRequestItemResource($updatedItem)
        );
    }



    public function destroy(TransferRequest $transferRequest, Request $request)
    {
        $itemPivot = TransferRequestItem::where('item_id', $request->item_id)
            ->where('transfer_request_id', $transferRequest->id)
            ->first();

        if (!$itemPivot) {
            return $this->error(
                status: Response::HTTP_UNPROCESSABLE_ENTITY,
                message: 'Item not found in this transfer request'
            );
        }


        $itemToDelete = $transferRequest->items()
            ->where('item_id', $request->item_id)
            ->first();


        $transferRequest->items()->detach($request->item_id);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Item deleted successfully',
            data: new ShowTransferRequestResource($transferRequest->load('items'))
        );
    }

}
