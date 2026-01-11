<?php

namespace App\Http\Requests\App\TransferRequest;

use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTransferRequest extends FormRequest
{
    use Responses;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentUserStoreId = request()->user()->store_id;

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'other_store_id' => [
                'required',
                'exists:Store,ID',
                function ($attribute, $value, $fail) use ($currentUserStoreId) {
                    if ($value == $currentUserStoreId) {
                        $fail('The destination store must be different from your current store.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'title.string' => 'The title must be a valid string.',

            'other_store_id.required' => 'You must specify a destination store.',
            'other_store_id.exists' => 'The selected store does not exist.',
        ];
    }

    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        $first_error = $validator->errors()->first();

        throw new HttpResponseException(
            $this->error(
                status: 422,
                message: $first_error,
                data: null
            )
        );
    }
}
