<?php

namespace App\Http\Requests\TemperatureRange;

use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTempRequest extends FormRequest
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
            'department' => 'string|sometimes|nullable',
            'min_temp'   => 'numeric|nullable',
            'max_temp'   => 'numeric|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'department.string'   => 'The department must be a valid string.',
            'min-temp.numeric' => 'The department must be a numerical number.',
            'max-temp.numeric' => 'The department must be a numerical number.',

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
