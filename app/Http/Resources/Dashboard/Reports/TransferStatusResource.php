<?php

namespace App\Http\Resources\Dashboard\Reports;

use App\Enums\PurchaseOrderTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'purchase_order' => [
                'id' => $this->ID,
                'title' => $this->POtitle,
                'number' => $this->PONumber,
                'type' => PurchaseOrderTypeEnum::tryFrom($this->POType)->name,
                'created_at' => $this->DateCreated->format('Y-m-d H:i:s'),
            ],

            'ship_to' => [
                'store_name' => $this->otherStore?->Name,
                'contact' => '',
                'address' => $this->otherStore?->Address1,
                'phone' => $this->otherStore?->PhoneNumber,
                'fax' => $this->otherStore?->FaxNumber
            ],

            'ship_from' => [
                'store_name' => $this->currentStore?->Name,
                'contact' => '',
                'address' => $this->currentStore?->Address1,
                'phone' => $this->currentStore?->PhoneNumber,
                'fax' => $this->currentStore?->FaxNumber
            ],

            'vehicle' => new ConditionResource($this->whenLoaded('condition')),

            'items' =>
                EntryResource::collection($this->whenLoaded('entries')),
            'total_units_received' => $this->whenLoaded(
                'entries',
                fn($entries) => $entries->sum('QuantityReceived')
            ),
        ];
    }
}
