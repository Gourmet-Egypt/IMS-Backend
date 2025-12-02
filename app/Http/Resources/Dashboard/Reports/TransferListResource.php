<?php

namespace App\Http\Resources\Dashboard\Reports;

use App\Enums\PurchaseOrderTypeEnum;
use App\Enums\TransferRequestStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'po_number' => $this->PONumber,
            'title' => $this->POTitle,
            'store_from' => $this->currentStore->Name,
            'store_receive' => $this->otherStore?->Name,
            'date' => $this->DateCreated,
            'status' => TransferRequestStatusEnum::fromInt($this->Status),
            'type' => PurchaseOrderTypeEnum::fromValue($this->POType)?->label()
        ];
    }
}
