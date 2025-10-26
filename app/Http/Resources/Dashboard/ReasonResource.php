<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReasonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ,
            'reason_type' => $this->reason_type ,
            'description' => $this->description ,
            'created_at' => $this->created_at ,
            'updated_at' => $this->updated_at
        ];
    }
}
