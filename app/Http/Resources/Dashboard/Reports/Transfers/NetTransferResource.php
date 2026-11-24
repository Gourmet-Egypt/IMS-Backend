<?php

namespace App\Http\Resources\Dashboard\Reports\Transfers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NetTransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->transferLines()->map(fn (array $line) => [
            'item_lookup_code' => $line['item_lookup_code'],
            'description' => $line['description'],
            'store_issue' => $this->currentStore?->Name,
            'store_received' => $this->otherStore?->Name,
            'rcv_date' => $this->receivingOrder?->receivedAt()?->toDateTimeString(),
            'create_date' => $this->createdAt()?->toDateTimeString(),
            'transfer_number' => $this->PONumber,
            'qty_issue' => $line['qty_issue'],
            'qty_rcv' => $line['qty_rcv'],
            'received_cost' => $line['received_cost'],
            'total_cost_rcv' => $line['total_cost_rcv'],
            'issue_cost' => $line['issue_cost'],
            'total_cost_issue' => $line['total_cost_issue'],
            'username' => $this->inventoryLog?->cashier?->Name,
            'diff_cost' => $line['diff_cost'],
        ])->all();
    }
}
