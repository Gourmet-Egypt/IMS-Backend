<?php

namespace App\Http\Requests\App\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class CommitOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $poType = $this->poType;
        $isPartialCommit = $this->route()->getActionMethod() === 'partialCommitOrder';

        $rules = match ($poType) {
            2 => [
                'Vehicle_tempIN' => $isPartialCommit
                    ? [
                        'nullable',
                        'required_if:isClosed,1',
                        'numeric',
                        'min:-50',
                        'max:50',
                    ]
                    : [
                        'required',
                        'numeric',
                        'min:-50',
                        'max:50',
                    ],
            ],

            3 => [
                'VehicleType' => ['required', 'string'],
                'Vehicle_tempOut' => ['required', 'numeric', 'min:-50', 'max:50'],
                'DeliveryPermitNumber' => ['required', 'string', 'max:255'],
                'Notes' => ['nullable', 'string', 'max:1000'],
                'seal_number' => ['required', 'string', 'max:1000'],
                'goods_type' => ['required', 'integer'],
                'driver_name' => ['nullable', 'string', 'max:255'],
                'vehicle_number' => ['nullable', 'string', 'max:50'],
            ],

            default => [],
        };

        if ($poType === 2 && $isPartialCommit) {
            $rules['isClosed'] = ['required', 'integer', 'in:0,1'];
        }

        return $rules;
    }


    public function messages(): array
    {
        return [
            'transactionType.required' => 'Transaction type is required.',
            'transactionType.in' => 'Transaction type must be either TransferOut or TransferIN.',

            // TransferOut messages
            'VehicleType.required' => 'Vehicle type is required for TransferOut transactions.',
            'seal_number.required' => 'seal_number is required for TransferOut transactions.',
            'VehicleType.in' => 'Invalid vehicle type selected.',
            'Vehicle_tempOut.required' => 'Vehicle temperature (Out) is required for TransferOut transactions.',
            'DeliveryPermitNumber.required' => 'Delivery permit number is required for TransferOut transactions.',
            'goods_type.required' => 'Goods type is required for TransferOut transactions.',
//            'driver_name.required' => 'Driver name is required for TransferOut transactions.',

            // TransferIN messages
            'Vehicle_tempIN.required' => 'Vehicle temperature (IN) is required for TransferIN transactions.',
            'isClosed.required' => 'isClosed is required for partial commit.',
            'isClosed.in' => 'isClosed must be 0 or 1.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'Order.VehicleType' => 'vehicle type',
            'Order.Vehicle_tempOut' => 'vehicle temperature (out)',
            'Order.Vehicle_tempIN' => 'vehicle temperature (in)',
            'Order.DeliveryPermitNumber' => 'delivery permit number',
        ];
    }
}
