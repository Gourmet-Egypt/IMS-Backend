<?php

namespace App\Services\Commit\Validators;

use App\Services\Commit\Contracts\CommitValidator;

class PurchaseOrderValidator implements CommitValidator
{
    public function rules(): array
    {
        return [
            'isClosed' => ['required', 'integer', 'in:0,1'],
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
