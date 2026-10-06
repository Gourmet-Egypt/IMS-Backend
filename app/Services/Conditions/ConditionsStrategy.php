<?php

namespace App\Services\Conditions;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

interface ConditionsStrategy
{
    public function endpoint(): string;

    public function rules(): array;

    public function messages(): array;

    public function buildPayload(PurchaseOrder $order, Request $request): array;
}
