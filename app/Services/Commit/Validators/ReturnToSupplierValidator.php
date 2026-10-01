<?php

namespace App\Services\Commit\Validators;

use App\Services\Commit\Contracts\CommitValidator;

class ReturnToSupplierValidator implements CommitValidator
{
    public function rules(): array
    {
        return [
            'transactionType' => ['required', 'string', 'in:ReturnToSupplier'],
        ];
    }

    public function messages(): array
    {
        return [
            'transactionType.required' => 'Transaction type is required.',
            'transactionType.in' => 'Transaction type must be ReturnToSupplier.',
        ];
    }
}
