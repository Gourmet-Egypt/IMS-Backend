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
