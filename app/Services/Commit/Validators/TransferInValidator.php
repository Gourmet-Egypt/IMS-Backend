<?php

namespace App\Services\Commit\Validators;

use App\Services\Commit\Contracts\CommitValidator;

class TransferInValidator implements CommitValidator
{
    public function rules(): array
    {
        return [
            'isClosed' => ['required', 'integer', 'in:0,1'],
            'vehicle_TempIN' => ['numeric', 'min:-50', 'max:50'],
            'vehicle_TempOut' => ['nullable', 'numeric', 'min:-50', 'max:50'],
            'vehicleType' => ['nullable', 'string', 'max:50'],
            'deliveryPermitNumber' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'seal_number' => ['nullable', 'string', 'max:50'],
            'driver_Name' => ['nullable', 'string', 'max:255'],
            'vehicle_Number' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'isClosed.required' => 'isClosed is required.',
            'isClosed.in' => 'isClosed must be 0 or 1.',
        ];
    }
}
