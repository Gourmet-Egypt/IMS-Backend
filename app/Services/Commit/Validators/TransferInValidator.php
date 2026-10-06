<?php

namespace App\Services\Commit\Validators;

use App\Services\Commit\Contracts\CommitValidator;

class TransferInValidator implements CommitValidator
{
    public function rules(): array
    {
        return [
            'isClosed'       => ['required', 'integer', 'in:0,1'],
            'Vehicle_tempIN' => ['nullable', 'required_without:vehicle_TempIN', 'numeric', 'min:-50', 'max:50'],
            'vehicle_TempIN' => ['nullable', 'numeric', 'min:-50', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'Vehicle_tempIN.required_without' => 'Vehicle temperature (IN) is required.',
            'isClosed.required'       => 'isClosed is required.',
            'isClosed.in'             => 'isClosed must be 0 or 1.',
        ];
    }
}
