<?php

namespace App\Http\Resources\Dashboard\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemInfoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'production_date' => $this->production_date,
            'expiration_date' => $this->expire_date,
        ];
    }
}
