<?php

namespace App\Http\Requests\Dashboard\Reason;

use App\Enums\ReasonEnum;
use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

class StoreReasonRequest extends FormRequest
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
        return [
            'reason_type' => ['required', 'string', new Enum(ReasonEnum::class)],
            'description'   => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'reason_type.required' => 'A reason_type is required.',
            'reason_type.string'   => 'The reason_type must be a valid string.',
            'reason_type.enum'     => 'The selected reason type is invalid.',

            'description.required' => 'You must specify a description.',
            'description.string' => 'The description must be a valid string.',

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
