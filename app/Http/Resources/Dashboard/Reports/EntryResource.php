<?php

namespace App\Http\Resources\Dashboard\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'lookupCode' => $this->Item->ItemLookupCode ?? '',
            'description' => $this->ItemDescription,
            'quantity_ordered' => $this->QuantityOrdered,
            'quantity_received' => $this->QuantityReceived,
            'difference' => $this->QuantityOrdered - $this->QuantityReceived,
            'item_data' => ItemInfoResource::collection($this->whenLoaded('infos')),
        ];
    }
}
