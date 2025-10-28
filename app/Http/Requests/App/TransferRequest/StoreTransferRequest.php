<?php

namespace App\Http\Requests\App\TransferRequest;

use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTransferRequest extends FormRequest
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
     * Validation rules.
     */
    public function rules(): array
    {
        $currentUserStoreId = auth()->user()?->store_id;

        return [
            'title' => ['required', 'string'],
            'to_store_id' => [
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
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A transfer request must have a title.',
            'title.string'   => 'The title must be a valid string.',

            'to_store_id.required' => 'You must specify a destination store.',
            'to_store_id.exists'   => 'The selected store does not exist.',
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
                data:null
            )
        );
    }
}
