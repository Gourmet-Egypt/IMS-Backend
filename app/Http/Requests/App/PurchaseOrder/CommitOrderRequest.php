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
        // Get PurchaseOrder from route parameter (matches {purchaseOrder} in route)
        $purchaseOrder = $this->route('purchaseOrder');
        $poType = $purchaseOrder ? (int) $purchaseOrder->POType : null;

        $rules = [];

        // POType 3 = TransferOut validation
        if ($poType === 3) {
            $rules['VehicleType'] = ['required', 'string'];
            $rules['Vehicle_tempOut'] = ['required', 'numeric', 'min:-50', 'max:50'];
            $rules['DeliveryPermitNumber'] = ['required', 'string', 'max:255'];
            $rules['Notes'] = ['nullable', 'string', 'max:1000'];
            $rules['seal_number'] = ['required', 'string', 'max:1000'];
            $rules['driver_name'] = ['nullable', 'string', 'max:255'];
            $rules['vehicle_number'] = ['nullable', 'string', 'max:50'];
        }

        // POType 2 = TransferIN validation
        if ($poType === 2) {
            $rules['Vehicle_tempIN'] = ['required', 'numeric', 'min:-50', 'max:50'];
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
//            'driver_name.required' => 'Driver name is required for TransferOut transactions.',

            // TransferIN messages
            'Vehicle_tempIN.required' => 'Vehicle temperature (IN) is required for TransferIN transactions.',
//            'receiver_name.required' => 'Receiver name is required for TransferIN transactions.',
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
