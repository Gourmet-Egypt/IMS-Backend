<?php

namespace App\Http\Resources\Dashboard\Reports\Supply;

use App\Models\PurchaseOrderEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MissingOnOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->storeEntries->map(function (PurchaseOrderEntry $entry) {
            $missing = (float) $entry->QuantityOrdered - (float) $entry->QuantityReceivedToDate;

            return [
                'item_lookup_code' => $entry->itemById?->ItemLookupCode,
                'description' => $entry->ItemDescription,
                'store' => $this->currentStore?->Name,
                'supplier' => $this->supplier?->SupplierName,
                'rcv_date' => $this->receivedAt()?->toDateTimeString(),
                'po_number' => $this->PONumber,
                'qty_missing' => $missing,
                'supplier_cost' => (float) $entry->Price,
                'total_cost' => round($missing * (float) $entry->Price, 4),
            ];
        })->all();
    }
}
