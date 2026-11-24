<?php

namespace App\Http\Resources\Dashboard\Reports\Supply;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplyProductionDateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $order = $this->purchaseOrder;
        $date = $order?->receivedAt() ?? $order?->createdAt();

        return [[
            'item_lookup_code' => $this->entry?->itemById?->ItemLookupCode,
            'description' => $this->entry?->ItemDescription,
            'store' => $order?->currentStore?->Name,
            'supplier' => $order?->supplier?->SupplierName,
            'rcv_date' => $date?->toDateTimeString(),
            'rcv_qty' => (float) ($this->quantity_IN ?? $this->quantity_issued),
            'po_number' => $order?->PONumber,
            'production_date' => $this->production_date?->toDateString(),
            'expiration_date' => $this->expire_date?->toDateString(),
            'ge_rtn_policy' => null,
            'display_period_days' => $date && $this->expire_date ? (int) $date->copy()->startOfDay()->diffInDays($this->expire_date, false) : null,
            'store_on_hand' => (float) ($this->entry?->stock?->Quantity ?? 0),
        ]];
    }
}
