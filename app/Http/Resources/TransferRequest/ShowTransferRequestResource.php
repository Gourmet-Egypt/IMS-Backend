<?php

namespace App\Http\Resources\TransferRequest;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowTransferRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'items' => $this->items->map(function ($item) {
                $pivot = \App\Models\TransferRequestItem::find($item->pivot->id);


                return [
                    'id' => $pivot->id,
                    'item_id' => $item->id,
                    'LookupCode' => $item->ItemLookupCode,
                    'quantity' => $pivot->quantity,
//                    'notes' => $pivot->notes,
                    'infos' => $pivot->itemInfos,
                ];
            }),

        ];

    }
}
