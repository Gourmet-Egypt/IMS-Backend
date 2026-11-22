<?php

namespace App\Http\Resources\Dashboard\Reports\Transfers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotReceivedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [[
            'store' => $this->otherStore?->Name,
            'order_number' => $this->PONumber,
            'order_date' => $this->createdAt()?->toDateTimeString(),
            'store_issue_name' => $this->currentStore?->Name,
            'hh_issue_log' => null,
            'receiving_status' => $this->receivingOrder ? 'open' : 'not_synced',
        ]];
    }
}
