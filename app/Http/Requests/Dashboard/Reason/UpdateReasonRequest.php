<?php

namespace App\Http\Requests\Dashboard\Reason;

use App\Enums\ReasonEnum;
use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

class UpdateReasonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'reason_type' => ['nullable', 'string', new Enum(ReasonEnum::class)],
            'description'   => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'reason_type.string'   => 'The reason_type must be a valid string.',
            'reason_type.enum'     => 'The selected reason type is invalid.',
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
