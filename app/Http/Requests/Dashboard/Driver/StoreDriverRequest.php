<?php

namespace App\Http\Requests\Dashboard\Driver;

use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDriverRequest extends FormRequest
{
    use Responses;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'employee_code' => ['required', 'string', 'max:50', 'unique:IMS_Drivers,employee_code'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name must not exceed 150 characters.',

            'employee_code.required' => 'The employee code field is required.',
            'employee_code.string' => 'The employee code must be a valid string.',
            'employee_code.max' => 'The employee code must not exceed 50 characters.',
            'employee_code.unique' => 'This employee code is already registered.',

            'phone.string' => 'The phone must be a valid string.',
            'phone.max' => 'The phone must not exceed 20 characters.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            $this->error(
                status: 422,
                message: $firstError,
            )
        );
    }
}
