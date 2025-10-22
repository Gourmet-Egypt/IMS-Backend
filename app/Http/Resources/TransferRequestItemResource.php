<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferRequestItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'transfer_request_id' => $this->pivot->transfer_request_id ?? $this->transfer_request_id,
            'item_id' => $this->pivot->item_id ?? $this->item_id,
            'LookupCode' => $this->pivot->item->ItemLookupCode,
            'Description' => $this->pivot->item->Description,
            'quantity' => $this->pivot->quantity ?? $this->quantity,
            'notes' => $this->pivot->notes ?? $this->notes,
            'created_at' => $this->pivot->created_at ?? $this->created_at,
            'updated_at' => $this->pivot->updated_at ?? $this->updated_at,

        ];
    }
}
