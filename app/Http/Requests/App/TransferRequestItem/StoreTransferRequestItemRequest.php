<?php

namespace App\Http\Requests\App\TransferRequestItem;

use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTransferRequestItemRequest extends FormRequest
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
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:Item,HQID',
            'quantity' => 'required|numeric',
            'notes' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages()
    {
        return [
            'id.required' => 'Item ID is required.',
            'id.exists' => 'The selected item does not exist.',

            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least 1.',

            'notes.string' => 'Notes must be a valid string.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $first_error = $validator->errors()->first();

        throw new HttpResponseException(
            $this->error(
                status: 422,
                message: 'Validation failed',
                data: $first_error
            )
        );
    }
}
