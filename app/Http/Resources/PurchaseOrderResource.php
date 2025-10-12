<?php

namespace App\Http\Resources;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PurchaseOrderEntryResource;

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
            'store_id' => $this->OtherStoreID ,
            'status' => $this->Status ,
            'created_at' => $this->DateCreated ,
            'entities' => PurchaseOrderEntryResource::collection($this->whenLoaded('entries')),
        ];
    }
}
