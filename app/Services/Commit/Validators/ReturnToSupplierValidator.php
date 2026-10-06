<?php

namespace App\Services\Commit\Validators;

use App\Services\Commit\Contracts\CommitValidator;

class ReturnToSupplierValidator implements CommitValidator
{
    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }
}
