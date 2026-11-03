<?php

namespace App\Http\Requests\App\TransferRequestItem;

use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BulkStoreTransferRequestItemRequest extends FormRequest
{
    use Responses;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|distinct|exists:Item,HQID',
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Item IDs are required.',
            'ids.array' => 'Item IDs must be an array.',
            'ids.*.exists' => 'One of the selected items does not exist.',
            'ids.*.distinct' => 'Item IDs must not contain duplicates.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->error(
                status: 422,
                message: 'Validation failed',
                data: $validator->errors()->first()
            )
        );
    }
}
