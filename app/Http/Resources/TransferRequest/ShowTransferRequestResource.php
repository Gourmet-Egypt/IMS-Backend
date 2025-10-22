<?php

namespace App\Http\Resources\TransferRequest;

use App\Http\Resources\TransferRequestItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowTransferRequestResource extends JsonResource
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
            'created_at' => $this->created_at,
            'items' => $this->whenLoaded('items', function () {
                return TransferRequestItemResource::collection($this->items);
            }),
        ];

    }
}
