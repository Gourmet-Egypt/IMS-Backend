<?php

namespace App\Http\Resources\App\Offline;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ID,
            'item_id' => $this->ItemID,
            'purchase_order_id' => $this->PurchaseOrderID,
            'LookupCode' => $this->item?->ItemLookupCode,
            'Description' => $this->ItemDescription,
            'price' => $this->Price,
            'quantity_on_hand' => $this->item?->Quantity,
            'quantity_ordered' => $this->QuantityOrdered,
            'quantity_received' => $this->QuantityReceived,
            'infos' => $this->whenLoaded('infos', function () {
                return PurchaseOrderEntryInfosResource::collection($this->whenLoaded('infos'));
            })
        ];
    }
}
