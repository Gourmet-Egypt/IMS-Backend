<?php

namespace App\Services\Commit\Validators;

use App\Services\Commit\Contracts\CommitValidator;

class TransferOutValidator implements CommitValidator
{
    public function rules(): array
    {
        return [
            'VehicleType'          => ['required', 'string'],
            'Vehicle_tempOut'      => ['nullable', 'numeric', 'min:-50', 'max:50'],
            'DeliveryPermitNumber' => ['nullable', 'string', 'max:255'],
            'Notes'                => ['nullable', 'string', 'max:1000'],
            'seal_number'          => ['required', 'string', 'max:1000'],
            'goods_type'           => ['nullable', 'integer'],
            'driver_name'          => ['nullable', 'string', 'max:255'],
            'vehicle_number'       => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'VehicleType.required'          => 'Vehicle type is required for TransferOut transactions.',
            'seal_number.required'          => 'seal_number is required for TransferOut transactions.',
        ];
    }
}
