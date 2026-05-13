<?php

namespace App\Http\Requests\Dashboard\Driver;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:150'],
            'employee_code' => ['nullable', 'string', 'max:50', Rule::unique('IMS_Drivers', 'employee_code')->ignore($this->driver)],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name must not exceed 150 characters.',

            'employee_code.string' => 'The employee code must be a valid string.',
            'employee_code.max' => 'The employee code must not exceed 50 characters.',
            'employee_code.unique' => 'This employee code is already registered.',

            'phone.string' => 'The phone must be a valid string.',
            'phone.max' => 'The phone must not exceed 20 characters.',

            'is_active.boolean' => 'The is_active field must be true or false.',
        ];
    }
}
