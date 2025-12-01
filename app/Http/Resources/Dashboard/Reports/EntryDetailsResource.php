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
            'lookupCode' => $this->item->ItemLookupCode ?? '',
            'description' => $this->item->Description ?? '',
            'department' => $this->item->department->Name ?? '',
            'category' => $this->item->category->Name ?? '',
            'total_cost' => ($this->item?->Cost ?? 0) * $this->QuantityOrdered,
            'tax_rate' => $this->TaxRate,
            'item_data' => $this->infos->map(function ($info) {
                return [
                    'production_date' => $info->production_date,
                    'expiration_date' => $info->expire_date,
                    'quantity_issued' => $info->quantity_issued,
                ];
            }),
        ];
    }
}
