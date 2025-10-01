<?php

namespace App\Http\Requests\TransferRequestItem;

use App\Traits\Responses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
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
    public function rules(): array
    {
        return [
            'transfer_request_id' => ['required', 'exists:transfer_requests,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:Item,ID'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'transfer_request_id.required' => 'Transfer request ID is required.',
            'transfer_request_id.exists' => 'The selected transfer request does not exist.',

            'items.required' => 'Items array is required.',
            'items.array' => 'Items must be an array.',
            'items.min' => 'At least one item is required.',

            'items.*.id.required' => 'Item ID is required for each item.',
            'items.*.id.exists' => 'One or more items do not exist.',

            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.numeric' => 'Quantity must be a number.',
            'items.*.quantity.min' => 'Quantity must be greater than 0.',

            'items.*.notes.string' => 'Notes must be a valid string.',
            'items.*.notes.max' => 'Notes cannot exceed 1000 characters.',
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
                status: 422 ,
                message: 'Validation failed',
                data: $first_error
            )
        );
    }
}
