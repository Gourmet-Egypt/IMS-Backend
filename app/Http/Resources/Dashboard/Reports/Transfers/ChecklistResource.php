<?php

namespace App\Http\Resources\Dashboard\Reports\Transfers;

use App\Http\Resources\Dashboard\Reports\StandardTemperature;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChecklistResource extends JsonResource
{
    public function __construct($resource, private readonly string $direction)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        $isOut = $this->direction === 'out';
        $own = $this->storeCondition;
        $condition = $own ?? $this->issuingOrder?->storeCondition;

        return [[
            $isOut ? 'store_issue_name' : 'store_rcv_name' => $this->currentStore?->Name,
            'order_number' => $this->PONumber,
            'car_temp' => $isOut ? $own?->vehicle_tempOut : ($own?->vehicle_tempIN ?? $condition?->vehicle_tempIN),
            'item_temp' => $condition?->goodsType?->name,
            'seal_number' => $condition?->seal_number,
            'driver_name' => $condition?->Driver_name,
            'car_number' => $condition?->Vehicle_number,
            'date' => $this->createdAt()?->toDateTimeString(),
            'standard_temp' => StandardTemperature::label($condition?->goodsType),
            'department' => $this->storeEntries
                ->map(fn ($entry) => $entry->itemById?->department?->Name)
                ->filter()->unique()->implode(', '),
        ]];
    }
}
