<?php

namespace App\Http\Resources\App\Item;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ID' => $this->ID,
            'ItemLookupCode' => $this->ItemLookupCode,
            'Description' => $this->Description,
            'HQID' => $this->HQID,
            'Aliases' => $this->aliases?->pluck('Alias'),
            'Price' => $this->Price,
            'Quantity' => $this->Quantity,
            'LastUpdated' => $this->LastUpdated,
            'DateCreated' => $this->DateCreated
        ];
    }
}
