<?php

namespace App\Http\Resources\App\Supplier;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ItemID,
            'lookupcode' => $this->item?->ItemLookupCode,
            'description' => $this->item?->Description,
        ];
    }
}
