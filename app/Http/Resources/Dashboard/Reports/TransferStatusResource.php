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
//        return [
//            'id' => $this->ID,
//            'title' => $this->POTitle,
//            'store_from' => $this->StoreID,
//            'store_from_name' => $this->currentStore->Name,
//            'store_receive' => $this->OtherStoreID,
//            'store_receive_name' => $this->otherStore->Name,
//            'date' => $this->DateCreated,
//            'order_number' => $this->PONumber,
//            'status' => TransferRequestStatusEnum::fromInt($this->Status)->value,
//            'entries' => EntryResource::collection($this->whenLoaded('entries')),
//        ];

        return [
            'purchase_order' => [
                'id' => $this->ID,
                'title' => $this->POtitle,
                'number' => $this->PONumber,
                'type' => PurchaseOrderTypeEnum::tryFrom($this->POType),
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
