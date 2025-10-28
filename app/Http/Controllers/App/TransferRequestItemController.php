<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;

use App\Http\Requests\App\TransferRequestItem\StoreTransferRequestItemRequest;
use App\Http\Resources\App\TransferRequestItemResource;
use App\Models\TransferRequest;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransferRequestItemController extends Controller
{
    use Responses;

    public function storeOrUpdate(StoreTransferRequestItemRequest $request, TransferRequest $transferRequest)
    {
        $itemId = $request->input('id');
        $exists = $transferRequest->items()->where('item_id', $itemId)->exists();

        $pivotData = [
            'quantity' => $request->input('quantity'),
            'notes' => $request->input('notes')
        ];

        if ($exists) {
            $transferRequest->items()->updateExistingPivot($itemId, $pivotData);
            $message = 'Item updated successfully';
        } else {
            $transferRequest->items()->attach($itemId, $pivotData);
            $message = 'Item added successfully';
        }

        $item = $transferRequest->items()
            ->where('item_id', $itemId)
            ->first();


        return $this->success(
            status: Response::HTTP_OK,
            message: $message,
            data: new TransferRequestItemResource($item)
        );
    }


    public function destroy(TransferRequest $transferRequest, Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer'
        ]);

        $itemId = $request->input('item_id');


        $exists = $transferRequest->items()->where('item_id', $itemId)->exists();

        if (!$exists) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Item not found in this transfer request'
            );
        }


        $item = $transferRequest->items()
            ->where('item_id', $itemId)
            ->first();


        $transferRequest->items()->detach($itemId);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Item removed successfully',
            data: new TransferRequestItemResource($item)
        );
    }
}
