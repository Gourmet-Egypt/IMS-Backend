<?php

namespace App\Http\Requests\App\Supplier;

use App\Models\Item;
use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class SearchSupplierItemRequest extends FormRequest
{
    use Responses;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lookupcode' => ['required', 'string', Rule::exists(Item::class, 'ItemLookupCode')],
        ];
    }

    public function messages(): array
    {
        return [
            'lookupcode.required' => 'Item lookup code is required.',
            'lookupcode.exists' => 'Item not found',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $status = isset($validator->failed()['lookupcode']['Exists'])
            ? Response::HTTP_NOT_FOUND
            : Response::HTTP_UNPROCESSABLE_ENTITY;

        throw new HttpResponseException(
            $this->error(
                status: $status,
                message: $validator->errors()->first(),
                data: null
            )
        );
    }
}
