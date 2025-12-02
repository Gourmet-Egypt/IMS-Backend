<?php

namespace App\Http\Resources\Dashboard\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'lookupCode' => $this->HQ_item->ItemLookupCode ?? '',
            'description' => $this->HQ_item->Description ?? '',
            'department' => $this->HQ_item->department->Name ?? '',
            'category' => $this->HQ_item->category->Name ?? '',
            'total_cost' => ($this->HQ_item?->Cost ?? 0) * $this->QuantityOrdered,
            'tax_rate' => $this->TaxRate,
            'total_quantity_issued' => $this->infos->sum('quantity_issued'),
            'item_data' => ItemInfoResource::collection($this->infos)
        ];
    }
}
