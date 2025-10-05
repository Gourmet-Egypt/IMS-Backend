<?php

namespace App\Http\Requests\TemperatureRange;

use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTempRequest extends FormRequest
{
    use Responses ;
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
            'department' => 'required|string',
            'min_temp'   => 'required|numeric',
            'max_temp'   => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'department.required' => 'A department must have a title.',
            'department.string'   => 'The department must be a valid string.',

            'min-temp.required' => 'You must specify a min-temp.',
            'min-temp.numeric' => 'The department must be a numerical number.',

            'max-temp.required' => 'You must specify a max-temp.',
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
