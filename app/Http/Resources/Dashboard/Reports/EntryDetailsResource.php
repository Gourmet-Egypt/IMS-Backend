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
            'lookupCode' => $this->itemById->ItemLookupCode ?? '',
            'description' => $this->itemById->Description ?? '',
            'department' => $this->itemById->department->Name ?? '',
            'category' => $this->itemById->category->Name ?? '',
            'total_cost' => ($this->itemById?->Cost ?? 0) * $this->QuantityOrdered,
            'tax_rate' => $this->TaxRate,
            'total_quantity_issued' => $this->infos->sum('quantity_issued'),
            'item_data' => ItemInfoResource::collection($this->infos)
        ];
    }
}
