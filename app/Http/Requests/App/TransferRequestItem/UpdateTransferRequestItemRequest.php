<?php

namespace App\Http\Requests\TransferRequestItem;

use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTransferRequestItemRequest extends FormRequest
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
            'item_id' => ['required', 'exists:Item,HQID'],
            'quantity' => ['nullable', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:1000'],
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

            'item_id.required' => 'Item ID is required.',
            'item_id.exists' => 'The selected item does not exist.',

            'quantity.numeric' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be greater than 0.',

            'notes.string' => 'Notes must be a valid string.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            $this->error(
                status: 422,
                message: "validation failed",
                data: $firstError)
        );
    }
}
