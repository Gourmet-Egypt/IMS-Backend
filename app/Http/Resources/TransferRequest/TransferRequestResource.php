<?php

namespace App\Http\Resources\TransferRequest;

use App\Http\Resources\TransferRequestItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->id,
            'title' => $this->title ,
            'from_store_id' => $this->from_store_id ,
            'to_store_id' => $this->to_store_id ,
            'to_store_name' => $this->transferToStore->Name ,
            'status' => $this->status ,
            'type' => $this->type ,
            'delivery_date' => $this->delivery_date ,
            'purchase_order_id' => $this->purchase_order_id ,
            'created_at' => $this->created_at ,
            'updated_at' => $this->updated_at ,
            'items' => $this->whenLoaded('items', function () {
                return TransferRequestItemResource::collection($this->items);
            }),

        ];
    }
}
