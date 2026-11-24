<?php

namespace App\Http\Resources\Dashboard\Reports\Supply;

use App\Models\PurchaseOrderEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierReturnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->storeEntries->map(fn (PurchaseOrderEntry $entry) => [
            'item_lookup_code' => $entry->itemById?->ItemLookupCode,
            'description' => $entry->ItemDescription,
            'supplier' => $this->supplier?->SupplierName,
            'store' => $this->currentStore?->Name,
            'rcv_date' => $this->receivedAt()?->toDateTimeString(),
            'create_date' => $this->createdAt()?->toDateTimeString(),
            'return_number' => $this->PONumber,
            'qty_issue' => (float) $entry->QuantityReceivedToDate,
            'supplier_cost' => (float) $entry->Price,
            'diff_qty' => (float) $entry->QuantityReceivedToDate - (float) $entry->QuantityOrdered,
        ])->all();
    }
}
