<?php

namespace App\Http\Resources\App\PurchaseOrder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderEntryResource extends JsonResource
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
            'lookupcode' => $this->item->ItemLookupCode,
            'description' => $this->ItemDescription,
            'price' => $this->Price,
            'quantity_ordered' => $this->QuantityOrdered,
            'quantity_received' => $this->QuantityReceived,
            'quantity_received_to_date ' => $this->QuantityReceivedToDate,
            'purchase_order_id' => $this->PurchaseOrderID,
            'quantity_issue' => $this->transferRequest->quantity_issue ?? null,
        ];
    }
}
