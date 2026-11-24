<?php

namespace App\Http\Resources\Dashboard\Reports\Transfers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionDateResource extends JsonResource
{
    public function __construct($resource, private readonly string $direction)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        $isOut = $this->direction === 'out';
        $order = $this->purchaseOrder;
        $date = $isOut ? $order?->createdAt() : ($order?->receivedAt() ?? $order?->createdAt());

        return [[
            'item_lookup_code' => $this->entry?->itemById?->ItemLookupCode,
            'description' => $this->entry?->ItemDescription,
            'store_issue' => $isOut ? $order?->currentStore?->Name : $order?->otherStore?->Name,
            'store_received' => $isOut ? $order?->otherStore?->Name : $order?->currentStore?->Name,
            $isOut ? 'issue_date' : 'rcv_date' => $date?->toDateTimeString(),
            $isOut ? 'issue_qty' : 'rcv_qty' => (float) ($isOut ? $this->quantity_issued : $this->quantity_IN),
            'transfer_number' => $order?->PONumber,
            'production_date' => $this->production_date?->toDateString(),
            'expiration_date' => $this->expire_date?->toDateString(),
            'ge_rtn_policy' => null,
            'display_period_days' => $date && $this->expire_date ? (int) $date->copy()->startOfDay()->diffInDays($this->expire_date, false) : null,
            'store_on_hand' => (float) ($this->entry?->stock?->Quantity ?? 0),
        ]];
    }
}
