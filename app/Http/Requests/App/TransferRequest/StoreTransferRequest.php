<?php

namespace App\Http\Requests\App\TransferRequest;

use App\Enums\TransferRequestTypeEnum;
use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
        $type = $this->input('type');

        return [
            'title' => ['sometimes', 'string', 'max:255'],

            'type' => [
                'required',
                'string',
                Rule::in(TransferRequestTypeEnum::cases())
            ],

            'other_store_id' => [
                'exists:Store,ID',
                'integer',
                function ($attribute, $value, $fail) use ($currentUserStoreId) {
                    if ($value == $currentUserStoreId) {
                        $fail('Cannot transfer from your own store');
                    }
                }
            ],
            'delivery_date' => [
                'sometimes',
                'date',
                'after_or_equal:today'
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
            'title.string' => 'The title must be a valid string.',
            'title.max' => 'The title cannot exceed 255 characters.',

            'type.required' => 'Transfer type is required.',
            'type.in' => 'Transfer type must be either TransferOut or TransferIn.',

            'other_store_id.required' => 'You must specify a destination store.',
            'other_store_id.exists' => 'The selected store does not exist.',
            'other_store_id.integer' => 'Invalid store ID format.',
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
