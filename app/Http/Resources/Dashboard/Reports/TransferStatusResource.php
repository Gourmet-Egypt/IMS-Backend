<?php

namespace App\Http\Resources\Dashboard\Reports;

use App\Enums\TransferRequestStatusEnum;
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
            'id' => $this->ID,
            'title' => $this->POTitle,
            'store_from' => $this->StoreID,
            'store_receive' => $this->OtherStoreID,
            'date' => $this->DateCreated,
            'order_number' => $this->PONumber,
            'status' => TransferRequestStatusEnum::fromInt($this->Status)->value,

            'entries' => $this->whenLoaded('entries', function () {
                return $this->entries->map(function ($entry) {
                    return [
                        'lookupCode' => $entry->Item->ItemLookupCode ?? '',
                        'description' => $entry->ItemDescription,
                        'quantity_received' => $entry->QuantityReceived,
                        'supplier_cost' => $entry->Item?->Cost ?? 0,
                        'total_cost' => ($entry->Item?->Cost ?? 0) * $entry->QuantityOrdered,
                        'item_data' => $entry->infos->map(function ($info) {
                            return [
                                'quantity' => $info->quantity_issued,
                                'production_date' => $info->production_date ?? '',
                                'expiration_date' => $info->expire_date ?? '',
                            ];
                        })->toArray(),
                    ];
                });
            }),
        ];
    }
}
