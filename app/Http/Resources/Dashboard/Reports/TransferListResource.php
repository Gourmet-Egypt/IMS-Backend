<?php

namespace App\Http\Resources\Dashboard\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ID,
            'title' => $this->POTitle,
            'store_from' => $this->StoreID,
            'store_receive' => $this->OtherStoreID,
            'date' => $this->DateCreated,

            'entries' => $this->whenLoaded('entries', function () {
                return $this->entries->map(function ($entry) {
                    return [
                        'id' => $entry->ID,
                        'lookupCode' => $entry->Item->ItemLookupCode ?? '',
                        'description' => $entry->ItemDescription,
                        'total_cost' => ($entry->Item?->Cost ?? 0) * $entry->QuantityOrdered,
                    ];
                });
            }),
        ];
    }
}
