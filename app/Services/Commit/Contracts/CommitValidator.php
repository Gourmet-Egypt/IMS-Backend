<?php

namespace App\Services\Commit\Contracts;

interface CommitValidator
{
    /**
     * Validation rules for this order type's single commit action.
     */
    public function rules(): array;

    /**
     * Custom validation messages for the rules above.
     */
    public function messages(): array;
}
