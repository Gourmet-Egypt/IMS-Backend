<?php

namespace App\Http\Resources\App\Offline;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ID ,
            'title' => $this->POTitle ,
            'type' => $this->POType ,
            'to_store_id' => $this->StoreID ,
            'from_store_id' => $this->OtherStoreID ,
            'created_at' => $this->DateCreated ,
            'items' => $this->whenLoaded('entries', function () {
                return PurchaseOrderEntryResource::collection($this->entries);
            }) ,
            'condition' => $this->whenLoaded('condition', function () {
                return new PurchaseOrderConditionResource($this->condition) ;
            })
        ];
    }
}
