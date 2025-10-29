<?php

namespace App\Http\Resources\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferedItemInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ,
            'item_id' => $this->purchase_order_entry_id ,
            'quantity_issued' => $this->quantity_issued ,
            'production_date' => $this->production_date ,
            'expire_date' => $this->expire_date ,
            'created_at' => $this->created_at
        ];
    }
}
