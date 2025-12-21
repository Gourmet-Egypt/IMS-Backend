<?php

namespace App\Http\Resources\App\Offline;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderConditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'vehicle_type' => $this->vehicle_type,
            'purchase_order_id' => $this->purchase_order_id,
            'vehicle_tempOut' => $this->vehicle_tempOut,
            'vehicle_tempIN' => $this->vehicle_tempIN,
            'delivery_permit_number' => $this->delivery_permit_number,
            'status' => $this->status,
            'notes' => $this->notes,
            'seal_number' => $this->seal_number,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
