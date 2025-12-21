<?php

namespace App\Http\Resources\App\TransferRequest;

use App\Enums\TransferRequestTypeEnum;
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
            'id' => $this->id,
            'title' => $this->title,
            'from_store_id' => $this->store_id,
            'to_store_id' => $this->other_store_id,
            'to_store_name' => $this->otherStore->Name,
            'status' => $this->status,
            'type' => TransferRequestTypeEnum::from($this->type)->number(),
            'delivery_date' => $this->delivery_date,
            'purchase_order_id' => $this->purchase_order_id,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),
            'items' => $this->whenLoaded('items', function () {
                return TransferRequestItemResource::collection($this->items);
            }),

        ];
    }
}
