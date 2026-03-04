<?php

namespace App\Http\Resources\Dashboard\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConditionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'vehicle_type' => $this->vehicle_type,
            'temperature_out' => $this->vehicle_tempOut,
            'temperature_in' => $this->vehicle_tempIN,
            'permit_number' => $this->delivery_permit_number,
            'seal_number' => $this->seal_number,
            'driver_name' => $this->Driver_name,
            'vehicle_number' => $this->Vehicle_number,
        ];
    }
}
