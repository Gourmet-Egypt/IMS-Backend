<?php

namespace App\Http\Requests\Dashboard\PurchaseOrderEmail;

use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePurchaseOrderEmailRequest extends FormRequest
{
    use Responses;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'store_id' => 'required|string|max:255',
            'employee_number' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'is_active' => 'nullable|string|in:0,1',
            'receive_all' => 'nullable|string|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name must not exceed 255 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',

            'store_id.required' => 'The store ID field is required.',
            'store_id.string' => 'The store ID must be a valid string.',
            'store_id.max' => 'The store ID must not exceed 255 characters.',

            'employee_number.string' => 'The employee number must be a valid string.',
            'employee_number.max' => 'The employee number must not exceed 255 characters.',

            'role.string' => 'The role must be a valid string.',
            'role.max' => 'The role must not exceed 255 characters.',

            'phone.string' => 'The phone must be a valid string.',
            'phone.max' => 'The phone must not exceed 255 characters.',

            'is_active.string' => 'The is_active must be a valid string.',
            'is_active.in' => 'The is_active must be either 0 or 1.',

            'receive_all.string' => 'The receive_all must be a valid string.',
            'receive_all.in' => 'The receive_all must be either 0 or 1.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            $this->error(
                status: 401,
                message: $firstError,
            )
        );
    }
}
