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
            'quantity_received' => $this->QuantityReceived,
            'supplier_cost' => $this->Item?->Cost ?? 0,
            'total_cost' => ($this->Item?->Cost ?? 0) * $this->QuantityOrdered,
            'total_quantity_issued' => $this->infos->sum('quantity_issued'),
            'item_data' => ItemInfoResource::collection($this->whenLoaded('infos')),
        ];
    }
}
