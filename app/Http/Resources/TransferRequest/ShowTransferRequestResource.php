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
                return [
                    'id' => $item->pivot->id,
                    'item_id' => $item->ID,
                    'LookupCode' => $item->ItemLookupCode,
                    'quantity' => $item->pivot->quantity,
                    'notes' => $item->pivot->notes ? utf8_encode($item->pivot->notes) : null,
                ];
            }),
        ];

    }
}
