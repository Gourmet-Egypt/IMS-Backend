<?php

namespace App\Services\Conditions\Strategies;

use App\Models\PurchaseOrder;
use App\Services\Conditions\ConditionsStrategy;
use Illuminate\Http\Request;

class PurchaseOrderConditionsStrategy implements ConditionsStrategy
{
    private const OPTIONAL = [
        'vehicle_type',
        'vehicle_tempOut',
        'vehicle_tempIN',
        'delivery_permit_number',
        'notes',
        'seal_number',
        'Driver_Name',
        'Vehicle_Number',
        'Goods_Type',
    ];

    public function endpoint(): string
    {
        return '/api/po-conditions';
    }

    public function rules(): array
    {
        return [
            'supplier_invoice_number' => ['required', 'string', 'max:255'],
            'vehicle_type'            => ['nullable', 'string'],
            'vehicle_tempOut'         => ['nullable', 'numeric', 'min:-50', 'max:50'],
            'vehicle_tempIN'          => ['nullable', 'numeric', 'min:-50', 'max:50'],
            'delivery_permit_number'  => ['nullable', 'string', 'max:255'],
            'notes'                   => ['nullable', 'string', 'max:1000'],
            'seal_number'             => ['nullable', 'string', 'max:1000'],
            'Driver_Name'             => ['nullable', 'string', 'max:255'],
            'Vehicle_Number'          => ['nullable', 'string', 'max:50'],
            'Goods_Type'              => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_invoice_number.required' => 'Supplier invoice number is required.',
        ];
    }

    public function buildPayload(PurchaseOrder $order, Request $request): array
    {
        return [
            'purchase_order_id'       => (int) $order->ID,
            'supplier_invoice_number' => (string) $request->input('supplier_invoice_number'),
        ] + array_filter(
            $request->only(self::OPTIONAL),
            fn ($value) => $value !== null
        );
    }
}
