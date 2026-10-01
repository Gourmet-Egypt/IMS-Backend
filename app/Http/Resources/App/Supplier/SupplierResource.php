<?php

namespace App\Http\Resources\App\Supplier;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ID,
            'hq_id' => $this->HQID,
            'code' => $this->Code,
            'supplier_name' => $this->SupplierName,
            'contact_name' => $this->ContactName,
            'phone_number' => $this->PhoneNumber,
            'email_address' => $this->EmailAddress,
            'country' => $this->Country,
            'state' => $this->State,
            'city' => $this->City,
            'last_updated' => $this->LastUpdated,
        ];
    }
}
