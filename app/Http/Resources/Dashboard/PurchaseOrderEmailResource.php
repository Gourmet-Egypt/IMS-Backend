<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderEmailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'store_id' => $this->store_id,
            'employee_number' => $this->employee_number,
            'role' => $this->role,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            'receive_all' => $this->receive_all,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
