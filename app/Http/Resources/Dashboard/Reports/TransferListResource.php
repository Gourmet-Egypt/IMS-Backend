<?php

namespace App\Http\Resources\Dashboard\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferListResource extends JsonResource
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
            'title' => $this->POTitle,
            'store_from' => $this->StoreID,
            'store_receive' => $this->OtherStoreID,

            'entries' => $this->whenLoaded('entries', function(){
                return $this->entries->map(function($entry){
                    return [
                        'lookupCode' => $entry->HQItem->ItemLookupCode ?? '',
                        'description' => $entry->ItemDescription,
                        'department' => $entry->HQItem->department->Name ?? '',
                        'category' => $entry->HQItem->category->Name ?? '',
                        'total_cost' => $entry->HQItem->cost * $entry->quantity ?? '',
                        'tax_rate' => $entry->TaxRate,
//                        'production_date' => $entry->infos?->production_date ?? '',
//                        'expired_date' => $entry->infos?->expire_date ?? '',

                    ];
                });
            }),
        ];
    }
}
