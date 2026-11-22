<?php

namespace App\Http\Resources\Dashboard\Reports\Supply;

use App\Http\Resources\Dashboard\Reports\StandardTemperature;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplyChecklistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $condition = $this->storeCondition;

        return [[
            'store_rcv_name' => $this->currentStore?->Name,
            'supplier_name' => $this->supplier?->SupplierName,
            'order_number' => $this->PONumber,
            'car_temp' => $condition?->vehicle_tempIN,
            'item_temp' => $condition?->goodsType?->name,
            'seal_number' => $condition?->seal_number,
            'driver_name' => $condition?->Driver_name,
            'car_number' => $condition?->Vehicle_number,
            'date' => $this->createdAt()?->toDateTimeString(),
            'standard_temp' => StandardTemperature::label($condition?->goodsType),
            'department' => $this->storeEntries
                ->map(fn ($entry) => $entry->itemById?->department?->Name)
                ->filter()->unique()->implode(', '),
            'supplier_invoice_number' => $condition?->supplier_invoice_number,
        ]];
    }
}
